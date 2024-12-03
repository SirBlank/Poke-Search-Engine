<html>
<head>
	<title>Pokedex</title>
	<!-- Import Bootstrap's classnames from CSS File -->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Google's Material Icons CSS File -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=search" />
<head>

<body>

<div class="container">
<h1 style="color: #CAA81D;">Pokedex</h1>
<p>Collection of all 1000 Pokemon from every generation.</p>
<p>Try searching for Bulbasaur (Pokemon names) or Grass (Pokemon types).</p>
<form action="search.php" method="post">
	<input type="text" size=40 name="search_string" value="<?php echo $_POST["search_string"];?>"/>
	<input type="submit" value="Search"/>
</form>

<!-- recommended line -->
<p>Related query: [insert here later based off previous queries]</p>


<?php
	if (isset($_POST["search_string"])) {
		$search_string = $_POST["search_string"];
		
		file_put_contents("logs.txt", $search_string.PHP_EOL, FILE_APPEND | LOCK_EX);

		$qfile = fopen("query.py", "w");

		fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
		fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
		fwrite($qfile, "index = pt.IndexFactory.of(\"./pokedex_index_v4/\")\n"); #Make sure to change the index name here
		fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n"); #Make sure to change the model here
		fwrite($qfile, "results = tf_idf.transform(queries)\n");

		for ($i=0; $i<5; $i++) {
			fwrite($qfile, "print(index.getMetaIndex().getItem(\"name\",results.docid[$i]))\n");
			fwrite($qfile, "print(index.getMetaIndex().getItem(\"description\",results.docid[$i]))\n");
   		}
   
   		fclose($qfile);

   		exec("ls | nc -u 127.0.0.1 10025"); #Make sure to change the port num here
   		sleep(3);

   		$stream = fopen("output", "r");

   		$line=fgets($stream);

   		while(($line=fgets($stream))!=false) {
			$name = trim($line); // First line is name
			$description = trim(fgets($stream)); // Next line is description
			$line = fgets($stream);
			echo "<a href=\"$url\">$name</a>: $description<br/>\n";
   		}

   		fclose($stream);
   
  		exec("rm query.py");
  		exec("rm output");
   		}
?>
<!-- other pokemon -->

<ul class="list-group">
    <?php { // probably needs for loop after php to create this card for each pokemon?>
        <li class="list-group-item d-flex align-items-center">
            <img src="[retrieve pokemon img]">
            <div>
                <p>[retrieve pokemon name]</p>
                <p>Type: [retrieve pokemon types]</p>
            </div>
        </li>
    <?php } ?>
</ul>

	</div>

</body>
</html>
