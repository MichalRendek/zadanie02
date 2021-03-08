<?php
require_once "config.php";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT * FROM `osoby`";
$stmt = $db->query($query);
$olympionists = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
    array_push($olympionists, $row['name'].' '.$row['surname']);

if (isset($_POST["name"]) && isset($_POST["surname"])) {
    $query = "INSERT INTO `osoby`(`name`, `surname`, `birth_day`, `birth_place`, `birth_country`, `death_day`, `death_place`, `death_country`) VALUES ('".$_POST['name']."','".$_POST['surname']."','".(!empty($_POST['birth_day']) ? date("j.n.Y", strtotime($_POST['birth_day'])) : '')."','".$_POST['birth_place']."','".$_POST['birth_country']."','".(!empty($_POST['death_day']) ? date("j.n.Y", strtotime($_POST['death_day'])) : '')."','".$_POST['death_place']."','".$_POST['death_country']."')";
    $stmt = $db->query($query);

    header('Location:add_placing.php?last_id='.$db->lastInsertId());
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
<body <?php echo isset($_POST["name"]) ? 'class="bg-success"' : ''; ?>>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-8 d-block m-auto">
            <h3 class="text-center my-4">Pridanie športovca</h3>
            <form id="form" class="mt-2" method="post" action="add_athlete.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Meno</label>
                    <input type="text" class="input-valid form-control" id="name" name="name">
                    <div class="div-valid invalid-feedback d-none">

                    </div>
                </div>
                <div class="mb-3">
                    <label for="surname" class="form-label">Priezvisko</label>
                    <input type="text" class="input-valid form-control" id="surname" name="surname">
                    <div class="div-valid invalid-feedback d-none">

                    </div>
                </div>
                <div class="mb-3">
                    <label for="birth_day" class="form-label">Dátum narodenia</label>
                    <input type="date" class="form-control" id="birth_day" name="birth_day">
                </div>
                <div class="mb-3">
                    <label for="birth_place" class="form-label">Miesto narodenia</label>
                    <input type="text" class="form-control" id="birth_place" name="birth_place">
                </div>
                <div class="mb-3">
                    <label for="birth_country" class="form-label">Krajina narodenia</label>
                    <input type="text" class="form-control" id="birth_country" name="birth_country">
                </div>
                <div class="mb-3">
                    <label for="death_day" class="form-label">Dátum úmrtia</label>
                    <input type="date" class="form-control" id="death_day" name="death_day">
                </div>
                <div class="mb-3">
                    <label for="death_place" class="form-label">Miesto úmrtia</label>
                    <input type="text" class="form-control" id="death_place" name="death_place">
                </div>
                <div class="mb-3">
                    <label for="death_country" class="form-label">Krajina úmrtia</label>
                    <input type="text" class="form-control" id="death_country" name="death_country">
                </div>
                <button type="submit" class="btn btn-warning d-block m-auto mb-4">Pridať</button>
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
                '<?php echo $info; ?>',
            <?php } ?>
        ]
        console.log(olympionist_info);

        $('#name').change(function (){
            validate_graphic();
        });
        $('#surname').change(function (){
            validate_graphic();
        });

        function validate_graphic() {
            let name = $('#name').val() + ' ' + $('#surname').val();
            if ($.inArray(name, olympionist_info) === -1) {
                $('.input-valid').removeClass('is-invalid').addClass('is-valid');
                $('.div-valid').addClass('d-none');
                correction = true;
            } else {
                $('.input-valid').removeClass('is-valid').addClass('is-invalid');
                $('.div-valid').removeClass('d-none').text("Športovec " + name + " v databáze už existuje!");
                correction = false;
            }
        }

        $( "#form" ).submit(function( event ) {
            event.preventDefault();
            if (correction)
                this.submit();
            else
                alert("Nesprávne údaje!");
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>