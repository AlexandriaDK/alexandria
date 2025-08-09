#!/usr/bin/env python3
"""
Import news from Alexandria RSS feed into the local database
"""
import os
import time
import requests
import mysql.connector
import xml.etree.ElementTree as ET
from datetime import datetime, timezone, timedelta
import html
import re

DB_HOST = os.environ.get('DB_HOST', 'db')
DB_USER = os.environ.get('DB_USER', 'alexuser')
DB_PASS = os.environ.get('DB_PASS', 'alexpass')
DB_NAME = os.environ.get('DB_NAME', 'alexandria')
RSS_URL = 'https://alexandria.dk/rss.php'

def wait_for_mysql():
    """Wait for MySQL to be ready"""
    for _ in range(60):
        try:
            conn = mysql.connector.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
            conn.close()
            return
        except Exception as e:
            print("Waiting for MySQL...", e)
            time.sleep(2)
    print("MySQL not ready after 2 minutes, exiting.")
    exit(1)

def fetch_rss():
    """Fetch RSS feed from Alexandria"""
    print(f"Downloading RSS from {RSS_URL}")
    resp = requests.get(RSS_URL)
    resp.raise_for_status()
    return resp.text

def parse_rss_date(date_str):
    """Convert RSS date format to MySQL datetime format"""
    try:
        # Parse: "Sat, 05 Jul 2025 14:43:31 +0200"
        # Extract just the date/time part, ignoring day name and timezone
        # Split by space and take elements 1-4: "05 Jul 2025 14:43:31"
        parts = date_str.split()
        if len(parts) >= 5:
            # Reconstruct without day name and timezone: "05 Jul 2025 14:43:31"
            date_part = ' '.join(parts[1:5])
            # Parse and format for MySQL
            dt = datetime.strptime(date_part, '%d %b %Y %H:%M:%S')
            return dt.strftime('%Y-%m-%d %H:%M:%S')
        else:
            raise ValueError("Unexpected date format")
    except (ValueError, IndexError):
        print(f"Warning: Could not parse date: {date_str}")
        return datetime.now().strftime('%Y-%m-%d %H:%M:%S')

def extract_news_id_from_guid(guid):
    """Extract news ID from GUID like 'https://alexandria.dk/#news_20090415000000_84'"""
    match = re.search(r'news_\d+_(\d+)$', guid)
    return int(match.group(1)) if match else None

def import_news(rss_content):
    """Parse RSS and import news into database"""
    conn = mysql.connector.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
    cursor = conn.cursor()
    
    # Parse XML
    root = ET.fromstring(rss_content)
    
    imported_count = 0
    skipped_count = 0
    
    # Find all item elements
    for item in root.findall('.//item'):
        title_elem = item.find('title')
        desc_elem = item.find('description')
        pubdate_elem = item.find('pubDate')
        guid_elem = item.find('guid')
        
        if title_elem is None or desc_elem is None or pubdate_elem is None or guid_elem is None:
            print("Warning: Skipping incomplete news item")
            continue
            
        # Extract data
        title = html.unescape(title_elem.text or '')
        description = html.unescape(desc_elem.text or '')
        pubdate_str = pubdate_elem.text or ''
        
        # Use the description as the news text since it contains the actual content with HTML
        # The description typically contains richer content like:
        # "Scenario list from <a href="data?con=1118" class="con">Hexcon 1998</a> has been added."
        news_text = description if description.strip() else title
        
        # Parse publication date
        published = parse_rss_date(pubdate_str)
        
        # Check if news already exists (by published date and text)
        cursor.execute(
            "SELECT id FROM news WHERE text = %s AND published = %s",
            (news_text, published)
        )
        
        if cursor.fetchone():
            skipped_count += 1
            continue
        
        # Insert news item
        cursor.execute(
            "INSERT INTO news (text, published, online) VALUES (%s, %s, 1)",
            (news_text, published)
        )
        
        imported_count += 1
        print(f"Imported: {news_text[:50]}... ({published})")
    
    conn.commit()
    cursor.close()
    conn.close()
    
    print(f"\nImport complete: {imported_count} imported, {skipped_count} skipped")

if __name__ == "__main__":
    wait_for_mysql()
    rss_content = fetch_rss()
    import_news(rss_content)
