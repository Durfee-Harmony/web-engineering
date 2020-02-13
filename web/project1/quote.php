<?php
require_once "dbConnect.php";
$db = get_db();

$id = filter_input(INPUT_POST, 'id');
if ($id == NULL) {
  $id = filter_input(INPUT_GET, 'id');
}

$quotes = $db->prepare("SELECT *
FROM quote q
  JOIN author_quote aq ON aq.quote_id = q.id
  JOIN author a ON a.id = aq.author_id
  JOIN quote_category qc ON q.id = qc.quote_id
  JOIN category c ON c.id = qc.category_id
WHERE q.id = :id");
$quotes->bindValue(':id', $id);
$quotes->execute();

echo "<h1>Quotes:</h1>";

while ($row = $quotes->fetch(PDO::FETCH_ASSOC)) {
  $author = $row["name"];
  $quote = $row["txt"];
  $cat = $row["cat"];
  echo "<br><h3>Author: $author</h3>";
  echo "<p>\"$quote\"</p>";
  echo "<p><strong>Category: $cat</strong></p>";
}
