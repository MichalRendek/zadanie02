<?php
require_once "config.php";

//$db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password);
$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT osoby.id, osoby.name, osoby.surname, oh.id AS oh_id, oh.year, oh.city, oh.type, umiestnenia.id AS umiestnenia_id, umiestnenia.discipline, umiestnenia.placing FROM osoby JOIN umiestnenia ON osoby.id = umiestnenia.person_id JOIN oh ON oh.id = umiestnenia.oh_id ORDER BY umiestnenia.placing, osoby.id";

$stmt = $db->query($query);
$olympionist_info = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    if (array_search($row['id'], array_column($olympionist_info, 7)) === false || $row['placing'] == 1){
        array_push($olympionist_info, [$row['name'], $row['surname'], $row['year'], $row['city'], $row['type'], $row['discipline'], $row['placing'], $row['id'], $row['oh_id'], $row['umiestnenia_id']]);
    }
}
// delete empty olimpionists
$query = "SELECT `id` FROM `osoby`";
$stmt = $db->query($query);
$empty_olimpionists = [];
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    if (array_search($row['id'], array_column($olympionist_info, 7)) === false){
        array_push($empty_olimpionists, $row['id']);
        $query = "DELETE FROM `osoby` WHERE id=".$row['id'];
        $db->query($query);
    }
}

$insert_id = 0;
$medal_count = 0;
$top_olympionists = [];
foreach ($olympionist_info as &$info) {
    if ($insert_id != $info[7]) {
        $insert_id = $info[7];
        array_push($top_olympionists, [$info[0], $info[1], array_count_values(array_column($olympionist_info, 7))[$info[7]]]);
    }
}
array_multisort(array_column($top_olympionists, 2), SORT_DESC, $top_olympionists);
$top_olympionists = array_slice($top_olympionists, 0, 10);
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

    <title>Olympiada</title>
</head>
<body>
<?php
if (!empty($empty_olimpionists))
    echo '
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Nezaradený športovci!</strong> V tabuľke športovci boli vymazaný športovci s id [ '.implode(", ", $empty_olimpionists).' ] pretože nemali priradené umiestnenie
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>'
?>

<div class="container">
    <h3 class="text-center my-4">Naši olympionisti</h3>
    <table class="table" id="table">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Meno</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="surname">Priezvisko</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="year">Rok</th>
            <th scope="col">Mesto</th>
            <th scope="col" style="cursor: pointer; text-decoration: underline" id="type">Typ</th>
            <th scope="col">Disciplína</th>
            <th scope="col">Akcie</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col d-flex justify-content-center">
            <a href="add_athlete.php" type="button" class="btn btn-success me-3">Pridať športovca</a>
            <a href="add_placing.php" type="button" class="btn btn-info">Pridať umiestnenie</a>
        </div>
    </div>

    <h3 class="text-center mb-4 mt-5">Najlepší olympionisti</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Meno</th>
                <th scope="col">Priezvisko</th>
                <th scope="col">Zlaté medaile</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($top_olympionists as &$info){ ?>
            <tr>
                <th scope="row"><?php echo $info[0] ?></th>
                <td><?php echo $info[1] ?></td>
                <td><?php echo $info[2] ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>


</div>

<script>
    var olympionist_info;

    $(document).ready(function () {
        olympionist_info = [
            <?php foreach ($olympionist_info as &$info) { ?>
                { name: '<?php echo $info[0] ?>', surname: '<?php echo $info[1] ?>', year: '<?php echo $info[2] ?>', city: '<?php echo $info[3] ?>', type: '<?php echo $info[4] ?>', discipline: '<?php echo $info[5] ?>', id: '<?php echo $info[7] ?>', oh_id: '<?php echo $info[8] ?>', umiestnenia_id: '<?php echo $info[9] ?>'},
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
    // by surname
    function compare_surname( a, b ) {
        if ( a.surname < b.surname ){
            return -1 * surname_sort;
        }
        if ( a.surname > b.surname ){
            return surname_sort;
        }
        return 0;
    }
    // by year
    function compare_year( a, b ) {
        if ( a.year < b.year ){
            return -1 * year_sort;
        }
        if ( a.year > b.year ){
            return year_sort;
        }
        return 0;
    }
    // by type / year
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
            table.append( '<tr><td scope="row">'+value.id+'</td><th><a style="text-decoration:none" href="detail.php?id=' + value.id + '">' + value.name +'<a></th><td>'+value.surname+'</td><td>'+value.year+'</td><td>'+value.city+'</td><td>'+value.type+'</td><td>'+value.discipline+'</td><td><button type="button"  name="'+ key +'" class="btn btn-warning me-2 change"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg></button><a type="button" href="delete.php?id='+ value.id +'" class="btn btn-danger delete"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></a></td></tr>' );
        });
        $('.change').click(function () {
            window.location.replace("edit.php?data="+JSON.stringify(olympionist_info[this.name]));
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>