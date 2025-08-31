<?php
require_once "./connect.php";
require_once "base.inc.php";

function addLocaleCountry($dbresult)
{
  foreach ($dbresult as $id => $data) {
    $dbresult[$id]['localecountry'] = Locale::getDisplayRegion("-" . $data['country'], LANG);
  }
  return $dbresult;
}

function conListByConfirmed($confirmed)
{
  $confirmed = (int) $confirmed;
  $list = getall("SELECT convention.id, convention.name, convention.begin, convention.end, convention.year, COALESCE(convention.country, conset.country) AS country FROM convention LEFT JOIN conset ON convention.conset_id = conset.id WHERE confirmed = $confirmed ORDER BY convention.year DESC, convention.name");
  $list = addLocaleCountry($list);
  return $list;
}

function conListByConfirmedGroup($confirmed)
{
  $confirmed = (int) $confirmed;
  $result = [];
  $list = getall("SELECT convention.id, convention.name, convention.begin, convention.end, convention.year, COALESCE(convention.country, conset.country) AS country FROM convention LEFT JOIN conset ON convention.conset_id = conset.id WHERE confirmed = $confirmed ORDER BY country, convention.year DESC, convention.name");
  foreach ($list as $convention) {
    if (!isset($result[$convention['country']])) {
      $result[$convention['country']] = ['countryname' => getCountryName($convention['country']), 'cons' => []];
    }
    $result[$convention['country']]['cons'][] = $convention;
  }
  uasort($result, function ($a, $b) {
    return count($b['cons']) - count($a['cons']);
  }); // sort array with most cons at top
  return $result;
}

function conListCountries($list)
{
  $count = [];
  foreach ($list as $con) {
    if ($con['country'] ?? "") {
      if (!isset($count[$con['country']])) {
        $count[$con['country']] = 0;
      }
      $count[$con['country']]++;
    }
  }
  arsort($count);
  $countries = [];
  foreach ($count as $country => $count) {
    $countries[$country] = getCountryName($country);
  }
  return $countries;
}

$cons_list    = conListByConfirmedGroup(1);
$cons_content = conListByConfirmedGroup(3);
$cons_missing = conListByConfirmedGroup(0);

$t->assign('todo_tabs', true);
$t->assign('cons_list', $cons_list);
$t->assign('cons_content', $cons_content);
$t->assign('cons_missing', $cons_missing);
$t->display('todo.tpl');
