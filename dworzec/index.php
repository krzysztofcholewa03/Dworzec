<?php
session_start();

require_once 'db.php';

// dane sesji (ulubione)
if (!isset($_SESSION['fav'])) {
    $_SESSION['fav'] = [];
}

// mechanizm przełącznika dla gwiazdki usuwanie/dodawanie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fav_id'])) {
    $id = (string)$_POST['fav_id'];

    if (in_array($id, $_SESSION['fav'], true)) {
        $_SESSION['fav'] = array_values(array_diff($_SESSION['fav'], [$id]));
    } else {
        $_SESSION['fav'][] = $id;
    }

    // f5 z filtrem
    $returnTo = $_POST['return_to'] ?? 'index.php';
    header('Location: ' . $returnTo);
    exit;
}

// czytanie filtrów i checkboxa czyli URL
$q = trim($_GET['q'] ?? '');
$onlyDelayed = (($_GET['delayed'] ?? '') === '1');


// interfejs wierszy w tabeli i klasa
interface RenderableRow {
    public function getId(): string;
    public function matchesQuery(string $q): bool;
    public function isDelayed(): bool;
    public function renderRow(bool $isFav, string $returnTo): string;
}

class Departure implements RenderableRow {
    public function __construct(
        private string $time,
        private string $dest,
        private int $platform,
        private string $number,
        private string $status
    ) {}
    
    //stabile id kursu | sprawdzenie czy to co wpisał użytkownik pasuje do wierszy | sprawdza czy jest opozniony w wierszsu
    public function getId(): string {
        return md5($this->time . '|' . $this->dest . '|' . $this->number);
    }


    public function matchesQuery(string $q): bool {
        if ($q === '') return true;
        $hay = mb_strtolower($this->dest . ' ' . $this->number);
        return mb_strpos($hay, mb_strtolower($q)) !== false;
    }
    

    public function isDelayed(): bool {
        return mb_strpos(mb_strtolower($this->status), 'opóźn') !== false;
    }
    
    //sprawdza czy jest w ulubionych
    public function renderRow(bool $isFav, string $returnTo): string {
        $id = htmlspecialchars($this->getId(), ENT_QUOTES, 'UTF-8');
        $star = $isFav ? '⭐' : '☆';

        // budowa wiersza i tworzenie HTML + dodaje przycisk gwiazdki
        return
            '<tr>' .
                '<td>' . htmlspecialchars($this->time, ENT_QUOTES, 'UTF-8') . '</td>' . 
                '<td>' . htmlspecialchars($this->dest, ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars((string)$this->platform, ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td>' . htmlspecialchars($this->number, ENT_QUOTES, 'UTF-8') . '</td>' .
                '<td class="d-flex justify-content-between align-items-center gap-2">' .
                    '<span>' . htmlspecialchars($this->status, ENT_QUOTES, 'UTF-8') . '</span>' .
                    '<form method="post" style="margin:0;" class="fav-form">' .
                        '<input type="hidden" name="fav_id" value="' . $id . '">' .
                        '<input type="hidden" name="return_to" value="' . htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') . '">' .
                        '<button type="submit" class="btn btn-sm btn-outline-light" title="Ulubione">' . $star . '</button>' .
                    '</form>' .
                '</td>' .
            '</tr>';
    }
}

//logika filtorwania zostawia tylko te kotre pasują do frazy którą wpisał użytkownik
class DepartureBoard {
    /** @param Departure[] $items */
    public function __construct(private array $items) {}
    
    /** @return Departure[] */
    public function filter(string $q, bool $onlyDelayed): array {
        return array_values(array_filter($this->items, function(Departure $d) use ($q, $onlyDelayed) {
            if (!$d->matchesQuery($q)) return false;
            if ($onlyDelayed && !$d->isDelayed()) return false;
            return true;
        }));
    }
}
?>
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

    <!--  Formularz filtrowania odjazdów oraz nieznikający tekst po wpisaniu -->
    <form class="filter-form mb-3" method="get" action="">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-9">
                <label class="form-label mb-1" for="q">Szukaj (kierunek / numer )</label>

                <input type="text" id="q" name="q" class="form-control"
                       placeholder="np. Warszawa, Nr"
                       value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <!--checkbox, przyciskski -->
            <div class="col-6 col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="delayed" name="delayed" value="1"
                        <?php echo $onlyDelayed ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="delayed">Tylko opóźnione</label>
                </div>
            </div>

            <div class="col-12 col-md-1 d-grid">
                <button type="submit" class="btn btn-primary">OK</button>
            </div>

            <div class="col-1 d-grid">
                <a class="btn btn-secondary btn-sm mt-2" href="index.php">Wyczyść filtry</a>
            </div>
        </div>
    </form>

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
                //wysłanie zapytania do bazdy danych i pobranie rekordów z tabeli
                $sql = "
                    SELECT
                        DATE_FORMAT(departure_time, '%H:%i') AS time,
                        dest,
                        platform,
                        train_number AS number,
                        status
                    FROM departures
                    ORDER BY departure_time ASC
                ";
                $stmt = $pdo->query($sql);
                $rows = $stmt->fetchAll();
                
                //tworzenie obiektów Departure
                $departures = [];
                foreach ($rows as $r) {
                    $departures[] = new Departure(
                        (string)$r['time'],
                        (string)$r['dest'],
                        (int)$r['platform'],
                        (string)$r['number'],
                        (string)$r['status']
                    );
                }

                // uruchamianie metody filter
                $board = new DepartureBoard($departures);
                $filtered = $board->filter($q, $onlyDelayed);
                $returnTo = $_SERVER['REQUEST_URI']; 

                // Pętla generująca wiersze dołączająca wartośc z tablicy
                if (count($filtered) === 0) {
                    echo '<tr><td colspan="5" class="text-center py-4">Brak wyników dla podanych filtrów.</td></tr>';
                } else {
                    foreach ($filtered as $d) {
                        $isFav = in_array($d->getId(), $_SESSION['fav'], true);
                        echo $d->renderRow($isFav, $returnTo);
                    }
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

        $rows.on("click", function (e) {

            //nie podświetlaj po gwiazdce
            if ($(e.target).closest("form").length) return;
            //nie podswietlaj wiersza brak wynikow
            if ($(this).children("td").length < 2) return;

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