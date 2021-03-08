<?php
require_once "config.php";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST["submit"])) {ň
    $query = "UPDATE `osoby` SET `name`= '".$_POST['name']."',`surname`= '".$_POST['surname']."' WHERE id = ".$_POST['id'].";";
    $query .= "UPDATE `oh` SET `type`= '".$_POST['type']."',`year`= ".$_POST['year'].",`city`= '".$_POST['city']."' WHERE id = ".$_POST['oh_id'].";";
    $query .= "UPDATE `umiestnenia` SET `discipline`= '".$_POST['discipline']."' WHERE id = ".$_POST['umiestnenia_id'].";";
    $stmt = $db->query($query);

    header('Location:index.php');
} else {
    $data = json_decode(htmlspecialchars_decode($_GET["data"]), true);
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <title>Olympiada editovanie</title>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-6 d-block m-auto">
            <h3 class="text-center my-4">Edit záznamu</h3>
            <form class="mt-2" method="post" action="edit.php">
                <input type="hidden" class="form-control" id="id" name="id" value="<?php echo $data['id'] ?>">
                <input type="hidden" class="form-control" id="oh_id" name="oh_id" value="<?php echo $data['oh_id'] ?>">
                <input type="hidden" class="form-control" id="umiestnenia_id" name="umiestnenia_id" value="<?php echo $data['umiestnenia_id'] ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Meno</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['name'] ?>">
                </div>
                <div class="mb-3">
                    <label for="surname" class="form-label">Priezvisko</label>
                    <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $data['surname'] ?>">
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Rok</label>
                    <input type="number" class="form-control" id="year" name="year" value="<?php echo $data['year'] ?>">
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">Mesto</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $data['city'] ?>">
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="type">Typ</label>
                    <select class="form-select" id="type" name="type">
                        <option <?php echo $data["type"] == 'LOH' ? 'selected' : ''; ?> value="LOH">LOH</option>
                        <option <?php echo $data["type"] == 'ZOH' ? 'selected' : ''; ?> value="ZOH">ZOH</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="discipline" class="form-label">Disciplína</label>
                    <input type="text" class="form-control" id="discipline" name="discipline" value="<?php echo $data['discipline'] ?>">
                </div>
                <button type="submit" name="submit" class="btn btn-warning d-block m-auto">Zmeniť</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>