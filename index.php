<?PHP
date_default_timezone_set('Europe/London');
$version="1.0.0";
if (file_exists("db/vgc.sqlite")) {
	$db = new PDO("sqlite:db/vgc.sqlite");
} else {
	$tmp = fopen("db/vgc.sqlite", "w");
	fclose($tmp);
	$db = new PDO('sqlite:db/vgc.sqlite');
	$db->exec("CREATE TABLE IF NOT EXISTS console (id INTEGER PRIMARY KEY AUTOINCREMENT, manufacturer TEXT, model TEXT, shortname TEXT);");
	$db->exec("CREATE TABLE IF NOT EXISTS games (id INTEGER PRIMARY KEY AUTOINCREMENT, console INTEGER, title TEXT, condition INTEGER, notes TEXT, formfactor TEXT);");
	$created=true;
}

if (isset($_POST['add'])) {
	if ((strtolower($_POST['add']) == "game")&&((!empty($_POST['title']))&&(!empty($_POST['condition']))&&(!empty($_POST['ff']))&&(!empty($_POST['console']))))  {
		if (empty($_POST['notes'])) { $notes = ""; } else { $notes = $_POST['notes']; }
		$qry = $db->prepare("INSERT INTO games ('id','console','title','condition','notes','formfactor') VALUES (NULL, :console, :title, :condition, :notes, :ff);");
		$qry->bindParam(':title',$_POST['title']);
		$qry->bindParam(':condition',$_POST['condition']);
		$qry->bindParam(':ff',$_POST['ff']);
		$qry->bindParam(':notes',$notes);
		$qry->bindParam(':console',$_POST['console']);
		$added = $qry->execute();
	} else if ((strtolower($_POST['add']) == "console")&&((!empty($_POST['man']))&&(!empty($_POST['mod']))&&(!empty($_POST['sn'])))) {
                $qry = $db->prepare("INSERT INTO console ('id','manufacturer','model','shortname') VALUES (NULL, :man, :mod, :sn);");
                $qry->bindParam(':man',$_POST['man']);
                $qry->bindParam(':mod',$_POST['mod']);
		$qry->bindParam(':sn',$_POST['sn']);
                $added = $qry->execute();
	}
} else if (isset($_POST['del'])) {
	if ((strtolower($_POST['del']) == "game")&&(!empty($_POST['id']))) {
		$qry = $db->prepare("DELETE FROM games WHERE id = :id;");
                $qry->bindParam(':id',$_POST['id']);
		$deleted = $qry->execute();
	} else if ((strtolower($_POST['del']) == "console")&&(!empty($_POST['id']))) {
                $qry = $db->prepare("DELETE FROM console WHERE id = :id;");
                $qry->bindParam(':id',$_POST['id']);
                $deleted = $qry->execute();
	}
}

$qry = $db->query("SELECT * FROM console ORDER BY id ASC;");
$consoles = $qry->fetchAll();

?>
<!DOCTYPE html>
<html lang="en-GB">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
    <meta name="description" content="A list of a personal video game collection."/>
    <meta name="generator" content="aboutcher-vgc <?php echo $version; ?>"/>
    <title>Video Game Collection</title>
    <link rel="shortcut icon" href="assets/img/favicon.png" />
    <link rel="icon" href="assets/img/favicon.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/fontawesome.all.css" />
    <link rel="stylesheet" href="assets/css/theme.css" />
  </head>
	<body data-spy="scroll" data-target="#consolelist" data-offset="55">
		<header>
      <div class="collapse bg-dark fixed-top" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 col-md-7 py-4">
              <h4 class="text-white">Add New Game</h4>
							<br/>
							<form method="POST">
  							<div class="form-group row">
									<label for="console" class="col-sm-3 col-form-label text-muted">Platform</label>
									<div class="col-sm-9">
										<select class="form-control" id="console" name="console">
											<option selected disabled>Select an Option</option>
											<?php
												foreach ($consoles as $console) {
													echo '<option value="'.$console['id'].'">'.$console['model'].'</option>';
												}
											?>
										</select>
	 								</div>
								</div>
								<div class="form-group row">
									<label for="ff" class="col-sm-3 col-form-label text-muted">Form Factor</label>
									<div class="col-sm-9">
										<select class="form-control" id="ff" name="ff">
											<option selected disabled>Select an Option</option>
											<option value="Disc">Disc</option>
											<option value="Cartridge">Cartridge</option>
											<option value="Card">Card</option>
											<option value="Digital">Digital</option>
											<option value="ROM Chip">ROM CHIP</option>
											<option value="ROM Dump">ROM Dump</option>
											<option value="Accessory">Accessory</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="title" class="col-sm-3 col-form-label text-muted">Title</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="title" name="title" placeholder="Half-Life 3">
									</div>
								</div>
								<div class="form-group row">
									<label for="condition" class="col-sm-3 col-form-label text-muted">Condition</label>
									<div class="col-sm-9">
										<select class="form-control" id="condition" name="condition">
											<option selected disabled name="condition" id="condition">Select an Option</option>
											<option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (Mint)</option>
											<option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; (Excellent)</option>
											<option value="3">&#9733;&#9733;&#9733;&#9734;&#9734; (Good)</option>
											<option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; (OK)</option>
											<option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; (Poor)</option>
											<option value="0">&#9734;&#9734;&#9734;&#9734;&#9734; (Ruined)</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="notes" class="col-sm-3 col-form-label text-muted">Notes</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="notes" name="notes" placeholder="..." />
									</div>
								</div>
								<input type="hidden" value="game" name="add"/>
								<div style="text-align:right;"><button type="submit" class="btn btn btn-outline-secondary mb-2"><i class="fas fa-save"></i> Save Game</button></div>
							</form>
            </div>
            <div class="d-none d-sm-none d-md-block col-md-4 offset-md-1 py-4">
							<h4 class="text-white">About</h4>
							<p class="text-muted">Video Game Collection or VGC for short was written by Adam Boutcher.</p>
							<p class="text-muted">It was primarily created for myself as a simple tool to keep track of my video game collection, YMMV.</p>
							<br/>
              <h4 class="text-white">Contact Me</h4>
              <ul class="list-unstyled">
                <li><a href="https://www.aboutcher.co.uk" class="text-white">Website</a></li>
                <li><a href="https://github.com/adamboutcher" class="text-white">GitHub</a></li>
                <li><a href="https://www.linkedin.com/in/adamboutcher/" class="text-white">LinkdIn</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar navbar-dark bg-dark box-shadow fixed-top">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
						<i class="fas fa-gamepad"></i>&nbsp;
						<strong>Video Game Collection</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
    </header>
		<div class="d-none d-sm-none d-md-block">
		<nav id="consolelist" class="navbar navbar-light bg-light justify-content-center shadow-sm fixed-bottom">
			<ul class="nav nav-pills nav-fill">
                          <?php
                             foreach ($consoles as $console) {
                               echo '<li class="nav-item"><a class="nav-link btn-light" href="#'.$console['shortname'].'">'.ucwords($console['manufacturer']).' '.ucwords($console['model']).'</a></li>';
                             }
                          ?>
   			  <li class="nav-item"><a class="nav-link btn-light" data-toggle="modal" data-target="#consoleModal"><i class="fas fa-plus"></i></a></li>
			</ul>
		</nav>
		</div>
		<main role="main">

		<?PHP
		if ((isset($created))&&($created === true)) {
			echo "<div class=\"container\" style=\"margin-top:65px;\"><div class=\"alert alert-info\">Database Generated.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div></div>";
		}
		if ((isset($added))&&($added === true)) {
			echo "<div class=\"container\" style=\"margin-top:65px;\"><div class=\"alert alert-success\">Item Added.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div></div>";
		}
		if ((isset($deleted))&&($deleted === true)) {
			echo "<div class=\"container\" style=\"margin-top:65px;\"><div class=\"alert alert-warning\">Item Removed.<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div></div>";
		}
		?>

			<?php
			$i=0;
			foreach ($consoles as $console) {
				$qry = $db->prepare("SELECT * FROM games WHERE console = :console ORDER BY title ASC;");
				$qry->bindParam(':console', $console['id']);
				$qry->execute();
				$games=$qry->fetchAll();
				?>
				<div class="py-5 <?php if($i%2==0){ echo "bg-light shadow-sm"; } ?>" id="<?php echo $console['shortname']; ?>"><div class="container">
					<h3 style="float:left;"><?php echo $console['manufacturer']." ".$console['model'];?></h3>
					<form method="post">
                                        	<input type="hidden" name="id" value="<?php echo $console['id']; ?>" />
                                                <input type="hidden" name="del" value="console" />
                                                <button data-toggle="tooltip" data-placement="left" title="Delete Console" type="submit" class="btn btn-sm btn-ss btn-light" style="float:right;"><i class="fas fa-trash-alt"></i></button>
                                        </form>
					<div tyle="clear:both";></div>
					<table class="table table-striped">
						<thead><tr><th scope="col">Title</th><th scope="col">Type</th><th scope="col">Condition</th><th scope="col">Notes</th><th style="width:65px;"></th></tr></thead>
						<tbody>
						<?php
						foreach ($games as $game) {
						?>
						<tr>
							<td><?php echo $game['title']; ?></td>
							<td><?php echo $game['formfactor']; ?></td>
							<td>
								<?php
								switch($game['condition']) {
									case 0:
										echo '<span data-toggle="tooltip" data-placement="top" title="Ruined">&#9734;&#9734;&#9734;&#9734;&#9734;</span>';
										break;
                                                                        case 1:
                                                                                echo '<span data-toggle="tooltip" data-placement="top" title="Poor">&#9733;&#9734;&#9734;&#9734;&#9734;</span>';
                                                                                break;
                                                                        case 2:
                                                                                echo '<span data-toggle="tooltip" data-placement="top" title="OK">&#9733;&#9733;&#9734;&#9734;&#9734;</span>';
                                                                                break;
                                                                        case 3:
                                                                                echo '<span data-toggle="tooltip" data-placement="top" title="Good">&#9733;&#9733;&#9733;&#9734;&#9734;</span>';
                                                                                break;
                                                                        case 4:
                                                                                echo '<span data-toggle="tooltip" data-placement="top" title="Excellent">&#9733;&#9733;&#9733;&#9733;&#9734;</span>';
                                                                                break;
                                                                        case 5:
                                                                                echo '<span data-toggle="tooltip" data-placement="top" title="Mint">&#9733;&#9733;&#9733;&#9733;&#9733;</span>';
                                                                                break;
								}
								?>

							</td>
							<td><?php echo $game['notes']; ?></td>
							<td class="text-right"><form method="post">
								<input type="hidden" name="id" value="<?php echo $game['id']; ?>" />
								<input type="hidden" name="del" value="game" />
								<button data-toggle="tooltip" data-placement="left" title="Delete Game" type="submit" class="btn btn-sm btn-ss btn-danger"><i class="fas fa-trash-alt"></i></button>
							</form></td>
						</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div></div>
				<?php
				$i++;
			}
			if (count($consoles) == 0 ) {
			?>
				<div class="py-5"><div class="container">
					<h3>No Platforms</h3>
					<p>The database contains no platforms&hellip;</p>
					<a class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#consoleModal">Add New Platform</a></p>
				</div></div>
			<?php
			}
			?>

		</main>
		<footer class="text-muted">
			<div class="container">
				<p><small>Video Game Collection database generated by <a href="https://github.com/adamboutcher/vgc"><em>aboutcher-vgc <?php echo $version; ?></em></a> by <a href="https://www.aboutcher.co.uk">Adam Boutcher</a>.</small></p>
			</div>
		</footer>

		<!-- Add new console modal -->
		<div class="modal fade" id="consoleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><div class="modal-dialog" role="document">	<div class="modal-content">
		<form method="post">
      		<div class="modal-header">
        		<h5 class="modal-title">Add New Platform</h5>
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      		</div>
      		<div class="modal-body">

					<div class="form-group row">
							<label for="man" class="col-sm-3 col-form-label text-muted">Manufacturer</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="man" name="man" placeholder="Sega">
							</div>
						</div>
						<div class="form-group row">
							<label for="mod" class="col-sm-3 col-form-label text-muted">Model</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="mod" name="mod" placeholder="Dreamcast 2">
							</div>
						</div>
                                                <div class="form-group row">
                                                        <label for="sn" class="col-sm-3 col-form-label text-muted">Short Name</label>
                                                        <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="sn" name="sn" placeholder="SDC2">
                                                        </div>
                                                </div>


					</div>
      		<div class="modal-footer">
			<input type="hidden" name="add" value="console" />
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        		<button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Console</button>
      		</div>
		</form>
    		</div></div></div>
		<!-- end modal -->

		<script src="assets/js/jquery-3.3.1.slim.min.js"></script>
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script>$(function () { $('[data-toggle="tooltip"]').tooltip() })</script>
	</body>
</html>
