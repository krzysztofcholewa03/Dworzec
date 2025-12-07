<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Tablica odjazdów</title>

    <!-- podpinanie Bootstrap, CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css">

    <!-- podpięcie JQuery do jego funkcji -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>
<body>
<header class="d-flex justify-content-between align-items-center">
    <h1 class="h4 mb-0 ">Dworzec Główny – najbliższe odjazdy</h1>
    <div id="clock" class="fw-bold"></div>
</header>

<main class="container">
    <!-- responsywnośc bootstrapowa -->
    <div class="table-responsive">
        <table class="custom-table table table-dark table-striped table-hover align-middle">

            <thead>
                <tr>
                    <th>Godzina</th>
                    <th>Kierunek</th>
                    <th>Peron</th>
                    <th>Nr</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
                //tablica z odjazdami, wiersze tabeli w php

                $departures = [
                    [
                        'time'        => '12:45',
                        'dest'        => 'Poznań Główny',
                        'platform'    => 3,
                        'number'      => 'R 7810',
                        'status'      => 'Planowy',
                    ],
                    [
                        'time'        => '13:10',
                        'dest'        => 'Warszawa',
                        'platform'    => 1,
                        'number'      => 'IC 4520',
                        'status'      => 'Opóźniony ~10 min',
                    ],
                    [
                        'time'        => '13:30',
                        'dest'        => 'Wrocław',
                        'platform'    => 2,
                        'number'      => 'R 9821',
                        'status'      => 'Planowy',
                    ],
                    [
                        'time'        => '14:05',
                        'dest'        => 'Gdańsk',
                        'platform'    => 4,
                        'number'      => 'IC 5301',
                        'status'      => 'Planowy',
                    ],
                    [
                        'time'        => '15:15',
                        'dest'        => 'Wolsztyn',
                        'platform'    => 3,
                        'number'      => 'WR 6767',
                        'status'      => 'Planowy',
                    ],
                    [
                        'time'        => '15:23',
                        'dest'        => 'Szczecin Główny',
                        'platform'    => 5,
                        'number'      => 'EC 2243',
                        'status'      => 'Planowy',
                    ],
                    [
                        'time'        => '15:37',
                        'dest'        => 'Leszno',
                        'platform'    => 2,
                        'number'      => 'CD 4432',
                        'status'      => 'Opóźniony ~5 min',
                    ],
                    [
                        'time'        => '15:54',
                        'dest'        => 'Lublin',
                        'platform'    => 1,
                        'number'      => 'B 7400',
                        'status'      => 'Planowy',
                    ],
                ];

                // Pętla generująca wiersze dołączająca wartośc z tablicy
                foreach ($departures as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['time'] . '</td>';
                    echo '<td>' . $row['dest'] . '</td>';
                    echo '<td>' . $row['platform'] . '</td>';
                    echo '<td>' . $row['number'] . '</td>';
                    echo '<td>' . $row['status'] . '</td>';

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
    <p id="selected-info" class="mt-3"></p>
</main>

<!-- funkcja JavaScript zegar -->
<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            now.toLocaleTimeString('pl-PL', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
    }

    updateClock();
    setInterval(updateClock, 1000);

    // funkcja jQuery klikanie, podświetlanie kursu
    $(function () {
        const $rows = $(".custom-table tbody tr");
        const $info = $("#selected-info");

        $rows.on("click", function () {

            $rows.removeClass("selected-row");

            $(this).addClass("selected-row");

            // pobieranie komórek z tabeli
            const time = $(this).children().eq(0).text();
            const dest = $(this).children().eq(1).text();
            const platform = $(this).children().eq(2).text();

            $info.text(`Wybrany kurs do ${dest} wyruszy o ${time} z peronu ${platform}.`);
        });
    });
</script>

</body>
</html>
