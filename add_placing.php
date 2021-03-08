<?php
require_once "config.php";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT * FROM `oh` ORDER BY id DESC";
$stmt = $db->query($query);
$oh = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    array_push($oh, [$row['id'], $row['type'], $row['year'], $row['city'], $row['country']]);

$query = "SELECT * FROM `osoby`";
$stmt = $db->query($query);
$olympionists = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    array_push($olympionists, [$row['id'], $row['name'], $row['surname']]);

if (isset($_POST["id"])) {
    $query = "INSERT INTO `umiestnenia`(`person_id`, `oh_id`, `placing`, `discipline`) VALUES (".$_POST['id'].",".$_POST['oh_id'].",'".$_POST['placing']."','".$_POST['discipline']."')";
    $stmt = $db->query($query);

    header('Location:index.php');
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

    <title>Pridanie umiestnenia</title>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-8 d-block m-auto">
            <h3 class="text-center my-4">Pridanie umiestnenia</h3>
            <form id="form" class="mt-2" method="post" action="add_placing.php">
                <div class="mb-3">
                    <label for="id" class="form-label">ID športovca</label>
                    <input type="number" class="form-control" id="id" name="id" value="<?php echo (isset($_GET["last_id"])?$_GET["last_id"]:'') ?>" <?php echo (isset($_GET["last_id"])?'readonly':'') ?>>
                    <div id="valid" class="">

                    </div>
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="oh_id">O. h.</label>
                    <select class="form-select" id="oh_id" name="oh_id">
                        <?php foreach ($oh as &$info){ ?>
                            <option value="<?php echo $info[0]; ?>"><?php echo $info[3].'~'.$info[4].'-'.$info[1].'-'.$info[2] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="placing" class="form-label">Umiestnenie</label>
                    <input type="number" class="form-control" id="placing" name="placing">
                </div>
                <div class="mb-3">
                    <label for="discipline" class="form-label">Disciplína</label>
                    <input type="text" class="form-control" id="discipline" name="discipline">
                </div>
                <button type="submit" class="btn btn-warning d-block m-auto">Pridať</button>
            </form>
        </div>
    </div>
</div>

<script>
    var olympionist_info;
    var correction = false;

    $(document).ready(function () {
        olympionist_info = [
            <?php foreach ($olympionists as &$info) { ?>
                { id: '<?php echo $info[0] ?>', name: '<?php echo $info[1] ?>', surname: '<?php echo $info[2] ?>'},
            <?php } ?>
        ]

        $('#id').change(function (){
            validate_graphic(this);
        });
        <?php echo (isset($_GET["last_id"])?"validate_graphic($('#id'));":""); ?>
        function validate_graphic(input) {
            $.each(olympionist_info, function (key, value) {
                if (value['id'] == $(input).val()) {
                    $(input).removeClass('is-invalid').addClass('is-valid');
                    $('#valid').removeClass('invalid-feedback').addClass('valid-feedback');
                    $('#valid').text(value['name'] + " " + value['surname']);
                    correction = true;
                    return false;
                } else {
                    $(input).removeClass('is-valid').addClass('is-invalid');
                    $('#valid').removeClass('valid-feedback').addClass('invalid-feedback');
                    $('#valid').text("Neexistujúce id športovca!");
                    correction = false;
                }
            });
        }

        $( "#form" ).submit(function( event ) {
            event.preventDefault();
            if (correction){
                window.onbeforeunload = null;
                this.submit();
            }
            else
                alert("Nesprávne údaje!");
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>