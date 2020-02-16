<?php
require_once "dbConnect.php";
$db = get_db();

function authors($db)
{
  $authors = $db->prepare("SELECT * FROM author");
  $authors->execute();
  $a = "<select name='author_select' id='author_select'>";
  while ($row = $authors->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $id = $row["id"];
    $a .= "<option id='$id'>$name</option>";
  }
  $a .= "</select>";
  return $a;
}

function categories($db)
{
  $categories = $db->prepare("SELECT * FROM category");
  $categories->execute();
  $c = "<select name='category_select' id='category_select'>";
  while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
    $cat = $row["cat"];
    $id = $row["id"];
    $c .= "<option id='$id'>$cat</option>";
  }
  $c .= "</select>";
  return $c;
}

function addQuote($txt, $author, $cat)
{
  echo "function add quote";
  $db = get_db();
  $s = $db->prepare("SELECT id FROM author WHERE name = $author");
  $s->execute();
  $a = $s->fetch(PDO::FETCH_ASSOC);
  $author_id = $a["id"];
  $s = $db->prepare("SELECT id FROM category WHERE cat = $cat");
  $s->execute();
  $c = $s->fetch(PDO::FETCH_ASSOC);
  $cat_id = $c["id"];

  try {
    echo "try";
    $query = "INSERT INTO quote (txt) VALUES (\"$txt\")";
    $statement = $db->prepare($query);
    $statement->execute();
    $quote = $db->prepare("SELECT MAX(id) FROM quote");
    $quote->execute();
    while ($row = $quote->fetch(PDO::FETCH_ASSOC)) {
      $quote_id = $row["id"];
      $add = "INSERT INTO author_quote (author_id, quote_id) VALUES ($author_id, $quote_id);";
      $add .= "INSERT INTO quote_category (category_id, quote_id) VALUES ($cat_id, $quote_id);";
      $state = $db->prepare($add);
      $state->execute();
      header("Location: quote.php/?id=$quote_id");
    }
  } catch (Exception $ex) {
    echo "Error with DB. Details: $ex";
    return false;
  }
  return true;
}


$i = filter_input(INPUT_POST, 'i');
if ($i == NULL) {
  $i = filter_input(INPUT_GET, 'i');
}

if ($i == 'q') {
  echo " add quote ";
  $txt = filter_input(INPUT_POST, 'txt');
  $author = filter_input(INPUT_POST, 'author_select');
  $cat = filter_input(INPUT_POST, 'category_select');
  $q = addQuote($txt, $author, $cat);
  if($q){
    echo "<br> quote added?";
  }
}
