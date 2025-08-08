import os
import time
import requests
import mysql.connector
import sys

DB_HOST = os.environ.get('DB_HOST', 'db')
DB_USER = os.environ.get('DB_USER', 'alexuser')
DB_PASS = os.environ.get('DB_PASS', 'alexpass')
DB_NAME = os.environ.get('DB_NAME', 'alexandria')
ALEXANDRIA_URL = os.environ.get('ALEXANDRIA_URL', 'https://alexandria.dk/en/export?dataset=all')

# Wait for MySQL to be ready
def wait_for_mysql():
    for _ in range(60):
        try:
            conn = mysql.connector.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
            conn.close()
            return
        except Exception as e:
            print("Waiting for MySQL...", e)
            time.sleep(2)
    print("MySQL not ready after 2 minutes, exiting.")
    sys.exit(1)

def fetch_data():
    print(f"Downloading data from {ALEXANDRIA_URL}")
    resp = requests.get(ALEXANDRIA_URL)
    resp.raise_for_status()
    return resp.json()['result']

def import_data(data):
    conn = mysql.connector.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
    cursor = conn.cursor()
    # Order is important due to foreign keys - updated based on dependency analysis
    table_order = [
        'persons', 'systems', 'genres', 'conventionsets', 'tags', 'titles', 'presentations', 'feeds',
        'magazines', 'awards', 'locations', 'sitetexts', 'games', 'conventions', 'issues', 'gametags', 'gameruns',
        'gamedescriptions', 'trivia', 'links', 'aliases', 'award_categories', 'award_nominees', 
        'award_nominee_entities', 'person_game_title_relations', 'game_convention_presentation_relations',
        'person_convention_relations', 'articles', 'contributors', 'article_reference', 'location_reference',
        'files'
    ]
    # Map JSON keys to actual table names
    table_map = {
        'persons': 'person',
        'systems': 'gamesystem',
        'genres': 'genre',
        'conventionsets': 'conset',
        'conventions': 'convention',
        'games': 'game',
        'titles': 'title',
        'presentations': 'presentation',
        'magazines': 'magazine',
        'issues': 'issue',
        'articles': 'article',
        'award_categories': 'award_categories',
        'award_nominees': 'award_nominees',
        'award_nominee_entities': 'award_nominee_entities',
        'files': 'files',
        'gamedescriptions': 'game_description',
        'gameruns': 'gamerun',
        'tags': 'tag',
        'gametags': 'tags',
        'trivia': 'trivia',
        'links': 'links',
        'contributors': 'contributor',
        'person_game_title_relations': 'pgrel',
        'game_convention_presentation_relations': 'cgrel',
        'person_convention_relations': 'pcrel',
        'article_reference': 'article_reference',
        'location_reference': 'lrel',
        'locations': 'locations',
        'aliases': 'alias',
        'sitetexts': 'weblanguages',
        'awards': 'awards',
        'genre_game_relations': 'ggrel'
    }
    
    for table in table_order:
        if table not in data:
            continue
        db_table = table_map.get(table, table)
        print(f"Importing {table} -> {db_table} ({len(data[table])} rows)")
        for row in data[table]:
            # Transform row data based on table-specific requirements
            if table == 'locations':
                # Convert latitude/longitude to MySQL POINT geometry
                if 'latitude' in row and 'longitude' in row:
                    # NOTE: The export query has ST_X(geo) AS latitude and ST_Y(geo) AS longitude
                    # But in MySQL POINT geometry: X=longitude, Y=latitude
                    # So the export labels are swapped - what's labeled 'latitude' is actually longitude
                    exported_lat = row.pop('latitude')    # This is actually longitude (X coordinate)
                    exported_lon = row.pop('longitude')   # This is actually latitude (Y coordinate)
                    if exported_lat and exported_lon:
                        # Create correct POINT: (longitude, latitude) = (exported_lat, exported_lon)
                        row['geo'] = f"ST_GeomFromText('POINT({exported_lat} {exported_lon})', 4326)"
            
            keys = ','.join(f'`{k}`' for k in row.keys())
            # Handle geometry values specially
            placeholders = []
            values = []
            for k, v in row.items():
                if k == 'geo' and isinstance(v, str) and v.startswith('ST_GeomFromText'):
                    placeholders.append(v)  # Insert raw SQL function
                else:
                    placeholders.append('%s')
                    values.append(v)
            
            values_clause = ','.join(placeholders)
            sql = f"INSERT IGNORE INTO `{db_table}` ({keys}) VALUES ({values_clause})"
            try:
                cursor.execute(sql, values)
            except Exception as e:
                print(f"Error inserting into {db_table}: {e}\nRow: {row}")
        conn.commit()
    
    # Set installation status to 'live' after successful import
    print("Setting installation status to 'live'...")
    cursor.execute("INSERT INTO `installation` (`key`, `value`) VALUES ('status', 'live') ON DUPLICATE KEY UPDATE `value` = 'live'")
    conn.commit()
    
    cursor.close()
    conn.close()
    print("Import complete.")

if __name__ == "__main__":
    wait_for_mysql()
    data = fetch_data()
    import_data(data)
