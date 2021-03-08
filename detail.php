<?php
require_once "config.php";

//$db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT osoby.id, osoby.name, osoby.surname, oh.id AS oh_id, oh.year, oh.city, oh.type, umiestnenia.id AS umiestnenia_id, umiestnenia.discipline, umiestnenia.placing FROM osoby JOIN umiestnenia ON osoby.id = umiestnenia.person_id JOIN oh ON oh.id = umiestnenia.oh_id WHERE osoby.id = ".$_GET['id']." ORDER BY umiestnenia.placing";

$stmt = $db->query($query);
$olympionist_info = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    array_push($olympionist_info, [$row['name'], $row['surname'], $row['year'], $row['city'], $row['type'], $row['discipline'], $row['placing'], $row['id'], $row['oh_id'], $row['umiestnenia_id']]);
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

    <title>Olympionik <?php echo $olympionist_info[0][0]; ?></title>
</head>
<body>

<div class="container">
    <h3 class="text-center my-4">Olympionik <?php echo $olympionist_info[0][0]; ?></h3>
    <table class="table" id="table">
        <thead>
        <tr>
            <th scope="col">Meno</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="surname">Priezvisko</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="year">Rok</th>
            <th scope="col">Mesto</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="type">Typ</th>
            <th scope="col">Disciplína</th>
            <th scope="col">Umiestnenie</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <a href="index.php" type="button" class="btn btn-success mt-5">Návrat</a>
</div>

<script>
    var olympionist_info;

    $(document).ready(function () {
        olympionist_info = [
            <?php foreach ($olympionist_info as &$info) { ?>
                { name: '<?php echo $info[0] ?>', surname: '<?php echo $info[1] ?>', year: '<?php echo $info[2] ?>', city: '<?php echo $info[3] ?>', type: '<?php echo $info[4] ?>', discipline: '<?php echo $info[5] ?>', placing: '<?php echo $info[6] ?>', id: '<?php echo $info[7] ?>'},
            <?php } ?>
        ]
        table_fill(olympionist_info);
    });

    let surname_sort = 1;
    $('#surname').click(function () {
        olympionist_info.sort( compare_surname );
        surname_sort *= -1;
        table_fill(olympionist_info);
    });
    let year_sort = 1;
    $('#year').click(function () {
        olympionist_info.sort( compare_year );
        year_sort *= -1;
        table_fill(olympionist_info);
    });
    let type_sort = 1;
    $('#type').click(function () {
        olympionist_info.sort( compare_type );
        type_sort *= -1;
        table_fill(olympionist_info);
    });

    // compare functions for sort
    // by name
    function compare_surname( a, b ) {
        if ( a.surname < b.surname ){
            return -1 * surname_sort;
        }
        if ( a.surname > b.surname ){
            return surname_sort;
        }
        return 0;
    }
    // by size
    function compare_year( a, b ) {
        if ( a.year < b.year ){
            return -1 * year_sort;
        }
        if ( a.year > b.year ){
            return year_sort;
        }
        return 0;
    }
    // by time
    function compare_type( a, b ) {
        if ( a.type === b.type ) {
            if ( a.year < b.year ){
                return 1;
            }
            if ( a.year > b.year ){
                return -1;
            }
            return 0;
        }
        if ( a.type < b.type ){
            return -1 * type_sort;
        }
        if ( a.type > b.type ){
            return type_sort;
        }
        return 0;
    }

    function table_fill(olympionist_info) {
        var table = $('#table>tbody');
        table.empty();
        $.each( olympionist_info, function( key, value ) {
            table.append( '<tr> <th scope="row"><a style="text-decoration:none" href="?id=' + value.id + '">' + value.name +'<a></th><td>'+value.surname+'</td><td>'+value.year+'</td><td>'+value.city+'</td><td>'+value.type+'</td><td>'+value.discipline+'</td><td>'+value.placing+'</td></tr>' );
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>