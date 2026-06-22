<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FishingSpot;
use App\Models\FishSpecies;
use App\Models\Amenity;
use App\Models\Review;
use App\Models\FishRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Główny Seeder Bazy Danych.
 * Odpowiada za automatyczne napełnienie bazy danych.
 * Tworzy:
 * - Konto administratora oraz konta wędkarzy
 * - Zestaw udogodnień na łowiskach
 * - Katalog gatunków ryb
 * - Bazę łowisk w 16 województwach z lokalizacją i opisami
 * - Opinie wędkarzy i rekordy ryb
 */ 
class DatabaseSeeder extends Seeder
{
    /**
     * Uruchamia seed bazy danych.
     * 
     * @return void
     */
    public function run(): void
    {
        // -------------------------------------------------------------------------
        // 1. UŻYTKOWNICY I UPRAWNIENIA
        // -------------------------------------------------------------------------
        
        // Utworzenie głównego konta administratora (role = admin)
        User::create([
            'name' => 'admin Admin',
            'email' => 'admin@lowisko.pl',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Tworzenie standardowych kont wędkarzy (role = user) do generowania opinii i rekordów
        $anglers = [
            User::create(['name' => 'Jan', 'email' => 'jan@lowisko.pl', 'password' => Hash::make('password'), 'role' => 'user']),
            User::create(['name' => 'Piotr', 'email' => 'piotr@lowisko.pl', 'password' => Hash::make('password'), 'role' => 'user']),
            User::create(['name' => 'Krzysztof', 'email' => 'krzysztof@lowisko.pl', 'password' => Hash::make('password'), 'role' => 'user']),
            User::create(['name' => 'Tomasz', 'email' => 'tomasz@lowisko.pl', 'password' => Hash::make('password'), 'role' => 'user']),
            User::create(['name' => 'Michał', 'email' => 'michal@lowisko.pl', 'password' => Hash::make('password'), 'role' => 'user']),
        ];

        // -------------------------------------------------------------------------
        // 2. UDOGODNIENIA NA ŁOWISKACH
        // -------------------------------------------------------------------------
        
        $amenityNames = [
            'Parking', 'Pomost wędkarski', 'Wypożyczalnia łodzi',
            'Slip do wodowania', 'Toaleta', 'Pole namiotowe',
            'Sklep wędkarski', 'Restauracja / Bar', 'Miejsce na ognisko',
            'Dojazd do samej wody'
        ];
        $amenityIds = [];
        foreach ($amenityNames as $name) {
            $amenityIds[] = Amenity::create(['name' => $name])->id;
        }

        // -------------------------------------------------------------------------
        // 3. GATUNKI RYB
        // -------------------------------------------------------------------------
        
        $speciesNames = [
            'Szczupak', 'Sandacz', 'Okoń', 'Sum', 'Karp', 'Amur',
            'Leszcz', 'Płoć', 'Lin', 'Karaś', 'Węgorz', 'Boleń',
            'Kleń', 'Jaź', 'Brzana', 'Pstrąg potokowy', 'Pstrąg tęczowy',
            'Lipień', 'Troć', 'Sieja'
        ];
        $speciesMap = [];
        foreach ($speciesNames as $name) {
            $speciesMap[$name] = FishSpecies::create(['name' => $name])->id;
        }

        // -------------------------------------------------------------------------
        // 4. SZABLONY OPINII WĘDKARZY
        // -------------------------------------------------------------------------
        
        $reviewTemplates = [
            "Miejscówka petarda! Udało mi się tu pobić życiówkę, jeśli chodzi o gatunek taki jak [:fish]. Woda czysta, ale presja spora w weekendy.",
            "Ciężkie łowisko, na dnie sporo zaczepów, straciłem kilka dobrych gum. Ale dla takich brań warto tu wracać.",
            "Piękne widoki i świetnie przygotowane stanowiska. Niestety tym razem brania słabe, głównie drobnica, ale klimat rekompensuje wszystko.",
            "Byłem w zeszły weekend, woda lekko opadła, ale [:fish] fajnie współpracował na płyciznach. Polecam zabrać echosondę na [:spot].",
            "Dojazd to jakiś dramat, zawieszenie można urwać na tych dziurach. Za to na wodzie cisza i spokój. Zdecydowanie polecam.",
            "Zdecydowanie moje ulubione miejsce na jesienne łowy. Uderzenia z opadu takie, że kij z rąk wyrywa!",
            "Gospodarz wody dba o zarybienie, co widać po wynikach. Złowiłem tu piękny okaz, polecam każdemu.",
            "Woda strasznie kapryśna. Jeden dzień wyciągasz sztukę za sztuką, a na drugi dzień totalna studnia. Mimo to lubię tu wracać.",
            "Fajne miejsce na wypad z pontonem. Z brzegu nie ma co szukać, bo strasznie zarasta, ale z wody można wyciągnąć super sztuki.",
            "Spędziłem tu cały weekend. O świcie [:fish] potrafi zrobić niezłe widowisko uderzając w drobnicę. Warto wstać wcześnie!",
            "Miejscami strasznie płytko, trzeba uważać na śrubę od silnika. Poza tym łowisko bardzo urokliwe i zadbane.",
            "Presja wędkarska robi swoje, ryby są bardzo ostrożne. Trzeba kombinować z kolorami przynęt i schodzić na cienkie plecionki.",
        ];

        // Łowiska
        $spots = [
            // Dolnośląskie
            ['name' => 'Rzeka Odra (Wrocław)', 'city' => 'Wrocław', 'region' => 'dolnośląskie', 'type' => 'rzeka', 'lat' => 51.1078, 'lng' => 17.0385, 'fish' => ['Kleń' => 25, 'Brzana' => 20, 'Boleń' => 20, 'Sandacz' => 20, 'Sum' => 15], 'desc' => 'Znakomity miejski odcinek Odry przy wrocławskich bulwarach. Liczne przelewy i mosty tworzą idealne stanowiska dla potężnych boleni i sprytnych kleni.'],
            ['name' => 'Zbiornik Mietkowski', 'city' => 'Mietków', 'region' => 'dolnośląskie', 'type' => 'zalew', 'lat' => 50.9634, 'lng' => 16.6187, 'fish' => ['Sandacz' => 45, 'Leszcz' => 30, 'Szczupak' => 15, 'Okoń' => 10], 'desc' => 'Ogromny zbiornik zaporowy u stóp góry Ślęży. Prawdziwa mekka dolnośląskich łowców sandaczy. Ze względu na specyfikę dna, łowienie z pontonu lub łodzi jest tu niemal obowiązkowe.'],
            ['name' => 'Jezioro Bystrzyckie', 'city' => 'Zagórze Śląskie', 'region' => 'dolnośląskie', 'type' => 'jezioro', 'lat' => 50.7512, 'lng' => 16.4231, 'fish' => ['Szczupak' => 35, 'Okoń' => 25, 'Pstrąg potokowy' => 20, 'Leszcz' => 20], 'desc' => 'Malowniczo położone jezioro zaporowe, wijące się w dolinie u podnóża Gór Sowich. Nad wodą góruje Zamek Grodno. Głębokie spady kryją piękne okazy okoni i szczupaków.'],
            ['name' => 'Zbiornik Słup', 'city' => 'Słup', 'region' => 'dolnośląskie', 'type' => 'zalew', 'lat' => 51.0967, 'lng' => 16.1012, 'fish' => ['Sandacz' => 35, 'Leszcz' => 35, 'Szczupak' => 20, 'Płoć' => 10], 'desc' => 'Zalew retencyjny na Nysie Szalonej z charakterystycznym, twardym i kamienistym dnem. Popularny cel weekendowych wyjazdów spinningistów polujących z opadu na "mętnoookiego".'],

            // Kujawsko-Pomorskie
            ['name' => 'Rzeka Wisła (Toruń)', 'city' => 'Toruń', 'region' => 'kujawsko-pomorskie', 'type' => 'rzeka', 'lat' => 53.0137, 'lng' => 18.5984, 'fish' => ['Sum' => 25, 'Sandacz' => 25, 'Boleń' => 20, 'Kleń' => 15, 'Leszcz' => 15], 'desc' => 'Dzika i szeroka Wisła przepływająca w cieniu toruńskiej starówki. Potężne wiślane główki to legendarne stanowiska grubych sumów i pięknych, wiślanych sandaczy.'],
            ['name' => 'Jezioro Gopło', 'city' => 'Kruszwica', 'region' => 'kujawsko-pomorskie', 'type' => 'jezioro', 'lat' => 52.5931, 'lng' => 18.3421, 'fish' => ['Sandacz' => 30, 'Węgorz' => 20, 'Leszcz' => 25, 'Szczupak' => 25], 'desc' => 'Jedno z najsłynniejszych jezior Kujaw, owiane legendą o Popielu i Mysiej Wieży. Płytkie, zarośnięte brzegi przeplatają się z głębszymi rynnami pełnymi drapieżników.'],
            ['name' => 'Zalew Włocławski', 'city' => 'Włocławek', 'region' => 'kujawsko-pomorskie', 'type' => 'zalew', 'lat' => 52.6245, 'lng' => 19.2456, 'fish' => ['Sum' => 35, 'Sandacz' => 30, 'Leszcz' => 20, 'Boleń' => 15], 'desc' => 'Największy sztuczny zbiornik w Polsce pod względem powierzchni. Bardzo kapryśna woda, znana z silnych wiatrów, ale wynagradzająca cierpliwość gigantycznymi sumami i wąsaczami.'],
            ['name' => 'Jezioro Żnińskie Duże', 'city' => 'Żnin', 'region' => 'kujawsko-pomorskie', 'type' => 'jezioro', 'lat' => 52.8512, 'lng' => 17.7234, 'fish' => ['Szczupak' => 35, 'Węgorz' => 20, 'Lin' => 25, 'Płoć' => 20], 'desc' => 'Serce malowniczych Pałuk. Akwen o silnie rozwiniętej linii brzegowej i licznych trzcinowiskach, będący naturalnym rajem dla lina i czającego się w zasadzce szczupaka.'],

            // Lubelskie
            ['name' => 'Rzeka Bug (Włodawa)', 'city' => 'Włodawa', 'region' => 'lubelskie', 'type' => 'rzeka', 'lat' => 51.5451, 'lng' => 23.5512, 'fish' => ['Sum' => 30, 'Sandacz' => 20, 'Szczupak' => 20, 'Boleń' => 15, 'Kleń' => 15], 'desc' => 'Graniczna, całkowicie dzika i nieujarzmiona rzeka Bug. Znajdziesz tu głębokie zakola, powalone drzewa do samej wody i wędkarski klimat, którego próżno szukać w innych regionach.'],
            ['name' => 'Jezioro Firlej', 'city' => 'Firlej', 'region' => 'lubelskie', 'type' => 'jezioro', 'lat' => 51.5589, 'lng' => 22.5087, 'fish' => ['Szczupak' => 35, 'Lin' => 25, 'Karaś' => 20, 'Węgorz' => 20], 'desc' => 'Otoczone lasami sosnowymi i piaszczystymi plażami płytkie jezioro. Choć latem oblężone przez turystów, jesienią staje się oazą spokoju i znakomitym łowiskiem białej ryby.'],
            ['name' => 'Jezioro Białe', 'city' => 'Okuninka', 'region' => 'lubelskie', 'type' => 'jezioro', 'lat' => 51.4981, 'lng' => 23.5352, 'fish' => ['Sieja' => 30, 'Szczupak' => 30, 'Okoń' => 20, 'Węgorz' => 20], 'desc' => 'Jedno z najczystszych jezior w tej części kraju, należące do Pojezierza Łęczyńsko-Włodawskiego. Kryształowo czysta woda wymaga odpowiedniego podejścia i finezyjnego sprzętu.'],
            ['name' => 'Zalew Zemborzycki', 'city' => 'Lublin', 'region' => 'lubelskie', 'type' => 'zalew', 'lat' => 51.1965, 'lng' => 22.5321, 'fish' => ['Sandacz' => 30, 'Leszcz' => 30, 'Karp' => 20, 'Sum' => 20], 'desc' => 'Lubelskie morze zaporowe na rzece Bystrzycy. Choć bliskość dużego miasta rodzi sporą presję, lokalni wędkarze wciąż chwalą się regularnymi łowieniem pięknych leszczy i sandaczy.'],

            // Lubuskie
            ['name' => 'Rzeka Warta (Gorzów)', 'city' => 'Gorzów Wlkp.', 'region' => 'lubuskie', 'type' => 'rzeka', 'lat' => 52.7367, 'lng' => 15.2287, 'fish' => ['Boleń' => 25, 'Sandacz' => 25, 'Kleń' => 20, 'Brzana' => 15, 'Sum' => 15], 'desc' => 'Szeroka Warta wpływająca w miejskie krajobrazy Gorzowa. Tutejsze ostrogi, warkocze i przelewy to podręcznikowe miejsca na brzanę oraz szybkie wiosenne bolenie.'],
            ['name' => 'Jezioro Niesłysz', 'city' => 'Niesulice', 'region' => 'lubuskie', 'type' => 'jezioro', 'lat' => 52.2312, 'lng' => 15.3345, 'fish' => ['Szczupak' => 35, 'Lin' => 25, 'Leszcz' => 20, 'Okoń' => 20], 'desc' => 'Ogromny, polodowcowy akwen słynący z czystej wody i pięknych, podwodnych łąk. Zatoki pełne powalonych drzew to wręcz idealne stanowiska na zasiadki z żywcem na szczupaka.'],
            ['name' => 'Jezioro Sławskie', 'city' => 'Sława', 'region' => 'lubuskie', 'type' => 'jezioro', 'lat' => 51.8923, 'lng' => 16.0234, 'fish' => ['Sandacz' => 30, 'Węgorz' => 25, 'Leszcz' => 25, 'Szczupak' => 20], 'desc' => 'Największe jezioro w południowej części województwa, nazywane Śląskim Morzem. Niezwykle zróżnicowana głębokość zmusza do użycia echosondy przy poszukiwaniu drapieżników.'],
            ['name' => 'Jezioro Łagowskie', 'city' => 'Łagów', 'region' => 'lubuskie', 'type' => 'jezioro', 'lat' => 52.3356, 'lng' => 15.2912, 'fish' => ['Szczupak' => 35, 'Okoń' => 30, 'Płoć' => 20, 'Lin' => 15], 'desc' => 'Rynnowe jezioro przecinające urokliwy Łagów. Strome brzegi porośnięte starym lasem bukowym sprawiają, że woda bywa tu chłodniejsza, a okonie dorastają do imponujących rozmiarów.'],

            // Łódzkie
            ['name' => 'Zalew Sulejowski', 'city' => 'Sulejów', 'region' => 'łódzkie', 'type' => 'zalew', 'lat' => 51.4231, 'lng' => 19.9543, 'fish' => ['Sandacz' => 40, 'Leszcz' => 30, 'Sum' => 15, 'Boleń' => 15], 'desc' => 'Kultowy zbiornik w centrum Polski na rzece Pilicy. Jest stosunkowo płytki, co sprawia, że wiosną szybko się nagrzewa. Dawniej był to absolutny raj dla spinningistów z całej Polski.'],
            ['name' => 'Zbiornik Jeziorsko', 'city' => 'Warta', 'region' => 'łódzkie', 'type' => 'zalew', 'lat' => 51.7823, 'lng' => 18.6812, 'fish' => ['Sandacz' => 35, 'Leszcz' => 30, 'Sum' => 20, 'Karp' => 15], 'desc' => 'Kolejny gigant zaporowy, tym razem na rzece Warcie. Jego południowa część to rezerwat ornitologiczny. Znany z potężnych fal i ogromnych stad białej ryby żerującej na płyciznach.'],
            ['name' => 'Rzeka Pilica (Tomaszów)', 'city' => 'Tomaszów Maz.', 'region' => 'łódzkie', 'type' => 'rzeka', 'lat' => 51.5312, 'lng' => 20.0123, 'fish' => ['Kleń' => 30, 'Brzana' => 20, 'Jaź' => 20, 'Szczupak' => 30], 'desc' => 'Urokliwy, meandrujący odcinek Pilicy. Rzeka jest tu czysta, z piaszczystym dnem i licznymi warkoczami roślin. Idealne warunki do łowienia z przepływanki na owady.'],
            ['name' => 'Zbiornik Cieszowice', 'city' => 'Kamieńsk', 'region' => 'łódzkie', 'type' => 'zalew', 'lat' => 51.3056, 'lng' => 19.4012, 'fish' => ['Karp' => 30, 'Amur' => 20, 'Szczupak' => 25, 'Karaś' => 25], 'desc' => 'Spokojny akwen retencyjny z widokiem na charakterystyczną Górę Kamieńsk. Świetnie sprawdza się w przypadku wędkarstwa gruntowego i metody karpiowej.'],

            // Małopolskie
            ['name' => 'Rzeka Dunajec (Nowy Sącz)', 'city' => 'Nowy Sącz', 'region' => 'małopolskie', 'type' => 'rzeka', 'lat' => 49.6212, 'lng' => 20.6956, 'fish' => ['Pstrąg potokowy' => 40, 'Lipień' => 30, 'Troć' => 15, 'Brzana' => 15], 'desc' => 'Piękna, górska rzeka z mocnym, bystrym nurtem. To teren legendarnych polskich muszkarzy, na którym łowi się pięknie ubarwione lipienie i kropkowane pstrągi.'],
            ['name' => 'Jezioro Rożnowskie', 'city' => 'Rożnów', 'region' => 'małopolskie', 'type' => 'jezioro', 'lat' => 49.7321, 'lng' => 20.6912, 'fish' => ['Sandacz' => 35, 'Leszcz' => 30, 'Sum' => 20, 'Boleń' => 15], 'desc' => 'Jezioro o niecodziennym kształcie, wijące się między wzgórzami Beskidu Wyspowego. Duże wahania wody zmuszają wędkarzy do ciągłego poszukiwania ryb na twardych stokach dna.'],
            ['name' => 'Jezioro Czorsztyńskie', 'city' => 'Czorsztyn', 'region' => 'małopolskie', 'type' => 'jezioro', 'lat' => 49.4356, 'lng' => 20.2789, 'fish' => ['Szczupak' => 30, 'Sandacz' => 20, 'Pstrąg potokowy' => 30, 'Leszcz' => 20], 'desc' => 'Zapierające dech w piersiach jezioro z widokiem na Tatry oraz zamki w Niedzicy i Czorsztynie. Chłodna, górska woda sprzyja występowaniu w niej rzadkich gatunków rzecznych.'],
            ['name' => 'Jezioro Mucharskie', 'city' => 'Mucharz', 'region' => 'małopolskie', 'type' => 'jezioro', 'lat' => 49.8156, 'lng' => 19.5312, 'fish' => ['Szczupak' => 35, 'Pstrąg tęczowy' => 25, 'Okoń' => 20, 'Karp' => 20], 'desc' => 'Stosunkowo młody zbiornik na rzece Skawie. Dno skrywa pozostałości zatopionych wsi i fragmenty lasów, które aktualnie są wybornymi kryjówkami dla wyrośniętych drapieżników.'],

            // Mazowieckie
            ['name' => 'Rzeka Wisła (Warszawa)', 'city' => 'Warszawa', 'region' => 'mazowieckie', 'type' => 'rzeka', 'lat' => 52.2297, 'lng' => 21.0122, 'fish' => ['Sandacz' => 30, 'Sum' => 30, 'Boleń' => 20, 'Kleń' => 20], 'desc' => 'Wiślane filary mostów i piaszczyste odsypy w sercu stolicy. Królestwo warszawskich wędkarzy. Na tym odcinku złowiono jedne z największych wiślanych sumów w historii Polski.'],
            ['name' => 'Zalew Zegrzyński', 'city' => 'Zegrze', 'region' => 'mazowieckie', 'type' => 'zalew', 'lat' => 52.4512, 'lng' => 21.0345, 'fish' => ['Sandacz' => 35, 'Sum' => 25, 'Leszcz' => 25, 'Boleń' => 15], 'desc' => 'Powstały ze spiętrzenia wód Narwi i Bugu "Zegrz" to kolebka nowoczesnego polskiego wędkarstwa podlodowego i jigowego. Woda poddana ogromnej presji, wymagająca od wędkarza kunsztu.'],
            ['name' => 'Rzeka Narew (Pułtusk)', 'city' => 'Pułtusk', 'region' => 'mazowieckie', 'type' => 'rzeka', 'lat' => 52.7056, 'lng' => 21.0812, 'fish' => ['Szczupak' => 30, 'Sum' => 20, 'Boleń' => 25, 'Kleń' => 25], 'desc' => 'Meandrująca, nizinna Narew z licznymi odnogami i starorzeczami w okolicach Pułtuska. Miejsca te, porośnięte grążelami, wprost pachną wiosennym szczupakiem.'],
            ['name' => 'Zalew Domaniowski', 'city' => 'Domaniów', 'region' => 'mazowieckie', 'type' => 'zalew', 'lat' => 51.4356, 'lng' => 20.8812, 'fish' => ['Sandacz' => 40, 'Leszcz' => 30, 'Sum' => 15, 'Szczupak' => 15], 'desc' => 'Spokojny zbiornik na Radomce. Z powodu wprowadzenia strefy ciszy wędkowanie z łodzi z silnikiem spalinowym jest tu zabronione, co sprzyja rozwojowi populacji pięknego sandacza.'],

            // Opolskie
            ['name' => 'Jezioro Nyskie', 'city' => 'Nysa', 'region' => 'opolskie', 'type' => 'jezioro', 'lat' => 50.4512, 'lng' => 17.2734, 'fish' => ['Sandacz' => 45, 'Leszcz' => 30, 'Okoń' => 15, 'Szczupak' => 10], 'desc' => 'Opolska duma i poligon doświadczalny przy połowie sandacza z brzegu. Oferuje piękne widoki na Góry Rychlebskie i charakteryzuje się bardzo zróżnicowanym dnem z podwodnymi górkami.'],
            ['name' => 'Jezioro Turawskie', 'city' => 'Turawa', 'region' => 'opolskie', 'type' => 'jezioro', 'lat' => 50.7256, 'lng' => 18.1289, 'fish' => ['Sandacz' => 40, 'Leszcz' => 30, 'Sum' => 15, 'Okoń' => 15], 'desc' => 'Tzw. Duże Jezioro Turawskie, od lat znane z "brań z opadu". Jesienią staje się areną spotkań wędkarzy, którzy przeczesują twarde stoki w poszukiwaniu wielkich ryb mętnoookich.'],
            ['name' => 'Jezioro Otmuchowskie', 'city' => 'Otmuchów', 'region' => 'opolskie', 'type' => 'jezioro', 'lat' => 50.4612, 'lng' => 17.1434, 'fish' => ['Sandacz' => 40, 'Leszcz' => 35, 'Sum' => 15, 'Płoć' => 10], 'desc' => 'Starszy brat Jeziora Nyskiego. Zabezpieczone długimi opaskami kamienistymi, wokół których skupia się nocne żerowanie potężnych sandaczy, a w dzień roi się tu od leszcza.'],
            ['name' => 'Zbiornik Michalice', 'city' => 'Namysłów', 'region' => 'opolskie', 'type' => 'zalew', 'lat' => 51.0967, 'lng' => 17.7312, 'fish' => ['Karp' => 30, 'Amur' => 20, 'Leszcz' => 30, 'Sandacz' => 20], 'desc' => 'Podłużny zalew na rzece Widawie, uwielbiany przez karpiarzy. Zarośnięte brzegi dają cień w upalne dni, a piaszczyste dno idealnie nadaje się do położenia zestawu gruntowego.'],

            // Podkarpackie
            ['name' => 'Rzeka San (Przemyśl)', 'city' => 'Przemyśl', 'region' => 'podkarpackie', 'type' => 'rzeka', 'lat' => 49.7833, 'lng' => 22.7667, 'fish' => ['Pstrąg potokowy' => 35, 'Lipień' => 30, 'Kleń' => 20, 'Brzana' => 15], 'desc' => 'Wędkarskie eldorado muchowe. Na wysokości Przemyśla San płynie szybkim nurtem po kamienistym dnie, rzeźbiąc niesamowite kryjówki dla kropkowanego pstrąga i szlachetnego lipienia.'],
            ['name' => 'Jezioro Solińskie', 'city' => 'Solina', 'region' => 'podkarpackie', 'type' => 'jezioro', 'lat' => 49.3952, 'lng' => 22.4531, 'fish' => ['Sandacz' => 30, 'Szczupak' => 25, 'Sum' => 25, 'Okoń' => 20], 'desc' => 'Bieszczadzkie morze, niesamowicie głębokie, charakteryzujące się stromymi spiekami i skałami. Tu nie łowi się z brzegu – prawdziwe wędkowanie zaczyna się na echsondzie i z łodzi.'],
            ['name' => 'Jezioro Tarnobrzeskie', 'city' => 'Tarnobrzeg', 'region' => 'podkarpackie', 'type' => 'jezioro', 'lat' => 50.5512, 'lng' => 21.6234, 'fish' => ['Szczupak' => 35, 'Okoń' => 30, 'Sandacz' => 20, 'Płoć' => 15], 'desc' => 'Zbiornik utworzony w wyrobisku dawnej kopalni siarki. Szczyci się krystalicznie czystą wodą, a jego bezdrzewne brzegi sprawiają wrażenie zupełnie obcego, księżycowego krajobrazu.'],
            ['name' => 'Zbiornik Sieniawa', 'city' => 'Sieniawa', 'region' => 'podkarpackie', 'type' => 'zalew', 'lat' => 49.6256, 'lng' => 21.9512, 'fish' => ['Szczupak' => 35, 'Sandacz' => 25, 'Leszcz' => 20, 'Okoń' => 20], 'desc' => 'Ukryty w Beskidzie Niskim mały, cichy zbiornik zaporowy na Wisłoku. Idealny na ucieczkę przed miejskim zgiełkiem. Rzuty w kierunku starego koryta rzeki często dają piękne szczupaki.'],

            // Podlaskie
            ['name' => 'Jezioro Wigry', 'city' => 'Stary Folwark', 'region' => 'podlaskie', 'type' => 'jezioro', 'lat' => 54.0312, 'lng' => 23.0645, 'fish' => ['Sieja' => 35, 'Szczupak' => 30, 'Węgorz' => 20, 'Okoń' => 15], 'desc' => 'Suwalski cud natury, nad którym góruje słynny klasztor kamedułów. Głębokie tonie kryją wielkie stada sielawy i potężne szczupaki. Wymagane jest zezwolenie Wigierskiego PN.'],
            ['name' => 'Jezioro Rajgrodzkie', 'city' => 'Rajgród', 'region' => 'podlaskie', 'type' => 'jezioro', 'lat' => 53.7656, 'lng' => 22.6812, 'fish' => ['Szczupak' => 30, 'Sandacz' => 25, 'Węgorz' => 25, 'Leszcz' => 20], 'desc' => 'Długie, rynnowe jezioro lodowcowe o rozwiniętej linii brzegowej i wysokich zboczach porośniętych lasem. Uznane łowisko polodowcowe pełne sandaczy z twardych szczytów podwodnych górek.'],
            ['name' => 'Jezioro Hańcza', 'city' => 'Błaskowizna', 'region' => 'podlaskie', 'type' => 'jezioro', 'lat' => 54.2612, 'lng' => 22.8134, 'fish' => ['Sieja' => 40, 'Szczupak' => 30, 'Pstrąg potokowy' => 15, 'Okoń' => 15], 'desc' => 'Najgłębsze jezioro w Polsce i całej środkowej Europie. Ze względu na niską temperaturę nawet latem, łowi się tu ryby wprost z epoki lodowcowej. Niesamowicie trudna, ale piękna woda.'],
            ['name' => 'Zbiornik Siemianówka', 'city' => 'Siemianówka', 'region' => 'podlaskie', 'type' => 'zalew', 'lat' => 52.9256, 'lng' => 23.8212, 'fish' => ['Szczupak' => 40, 'Karaś' => 25, 'Lin' => 20, 'Okoń' => 15], 'desc' => 'Dzikie jezioro zaporowe na wschodniej granicy kraju, wciśnięte w Puszczę Białowieską. Niesamowite ilości zatopionych pni drzew tworzą tu naturalne żerowiska drapieżników.'],

            // Pomorskie
            ['name' => 'Jezioro Wdzydze', 'city' => 'Wdzydze', 'region' => 'pomorskie', 'type' => 'jezioro', 'lat' => 53.9812, 'lng' => 17.9134, 'fish' => ['Troć' => 25, 'Szczupak' => 30, 'Okoń' => 25, 'Leszcz' => 20], 'desc' => 'Kompleks jezior w kształcie potężnego krzyża, pieszczotliwie nazywany "Kaszubskim Morzem". Ze względu na rozmiar ciężko czyta się tu wodę, co wymaga od spinningistów sporych umiejętności.'],
            ['name' => 'Jezioro Żarnowieckie', 'city' => 'Nadole', 'region' => 'pomorskie', 'type' => 'jezioro', 'lat' => 54.7556, 'lng' => 18.0512, 'fish' => ['Szczupak' => 40, 'Okoń' => 30, 'Płoć' => 15, 'Węgorz' => 15], 'desc' => 'Duże i bardzo wietrzne jezioro rynnowe leżące "rzut beretem" od Bałtyku. Posiada twarde dno. Legendarna woda dla łowców prawdziwych metrowych szczupaków "krokodyli".'],
            ['name' => 'Jezioro Łebsko', 'city' => 'Łeba', 'region' => 'pomorskie', 'type' => 'jezioro', 'lat' => 54.7312, 'lng' => 17.4089, 'fish' => ['Sandacz' => 30, 'Szczupak' => 30, 'Węgorz' => 25, 'Leszcz' => 15], 'desc' => 'Przymorskie, bardzo płytkie jezioro z dnem zdominowanym przez muł. Silnie odczuwalne są w nim morskie przypływy, co okresowo wciąga do akwenu słonawą wodę i nowe ryby.'],
            ['name' => 'Jezioro Charzykowskie', 'city' => 'Charzykowy', 'region' => 'pomorskie', 'type' => 'jezioro', 'lat' => 53.7556, 'lng' => 17.5012, 'fish' => ['Szczupak' => 35, 'Sandacz' => 25, 'Leszcz' => 20, 'Węgorz' => 20], 'desc' => 'Zlokalizowane w otulinie Parku Narodowego Bory Tucholskie. Bardzo zadbane łowisko, będące popularnym latem miejscem dla żeglarzy, a jesienią rajem dla pasjonatów spinningu.'],

            // Śląskie
            ['name' => 'Jezioro Żywieckie', 'city' => 'Żywiec', 'region' => 'śląskie', 'type' => 'jezioro', 'lat' => 49.7123, 'lng' => 19.2045, 'fish' => ['Sandacz' => 35, 'Leszcz' => 30, 'Szczupak' => 20, 'Sum' => 15], 'desc' => 'Malowniczy zalew u podnóża góry Żar. Skaliste brzegi i twarde zbocza opadające nagle na kilkanaście metrów sprawiają, że to najlepsze stanowiska na zębate sandacze w regionie.'],
            ['name' => 'Zbiornik Goczałkowicki', 'city' => 'Goczałkowice', 'region' => 'śląskie', 'type' => 'zalew', 'lat' => 49.9212, 'lng' => 18.8956, 'fish' => ['Sandacz' => 40, 'Leszcz' => 30, 'Sum' => 15, 'Karp' => 15], 'desc' => 'Ujęcie wody pitnej dla Śląska i "śląskie morze". Wędkowanie jest tu mocno regulowane i uwarunkowane surowym regulaminem, jednak rybostan, zwłaszcza sandaczowy, wciąż budzi podziw.'],
            ['name' => 'Rzeka Wisła (Ustroń)', 'city' => 'Ustroń', 'region' => 'śląskie', 'type' => 'rzeka', 'lat' => 49.7212, 'lng' => 18.8089, 'fish' => ['Pstrąg potokowy' => 45, 'Lipień' => 35, 'Pstrąg tęczowy' => 20], 'desc' => 'Królowa polskich rzek w swoim górskim, bystrym stadium. Usiana sztucznymi progami i kaskadami. Znakomite miejsce dla muszkarzy przedzierających się w spodniobutach za pstrągiem.'],
            ['name' => 'Zbiornik Poraj', 'city' => 'Poraj', 'region' => 'śląskie', 'type' => 'zalew', 'lat' => 50.6956, 'lng' => 19.2134, 'fish' => ['Sandacz' => 35, 'Leszcz' => 30, 'Sum' => 20, 'Szczupak' => 15], 'desc' => 'Utworzony na Warcie zbiornik leżący u progu Jury Krakowsko-Częstochowskiej. Obfituje w ukryte, zarośnięte zatoczki, z których wędkarze wyciągają potężne, medalowe drapieżniki.'],

            // Świętokrzyskie
            ['name' => 'Zalew Chańcza', 'city' => 'Korytnica', 'region' => 'świętokrzyskie', 'type' => 'zalew', 'lat' => 50.6212, 'lng' => 21.0545, 'fish' => ['Sandacz' => 35, 'Leszcz' => 30, 'Sum' => 20, 'Okoń' => 15], 'desc' => 'Zalew w Górach Świętokrzyskich z twardym, najeżonym głazami i zalanymi drogami dnem. Charakteryzuje się świetnym rybostanem drapieżników chętnie reagujących na gumowe przynęty.'],
            ['name' => 'Zbiornik Wióry', 'city' => 'Wióry', 'region' => 'świętokrzyskie', 'type' => 'zalew', 'lat' => 50.9456, 'lng' => 21.1512, 'fish' => ['Sandacz' => 30, 'Szczupak' => 30, 'Leszcz' => 25, 'Boleń' => 15], 'desc' => 'Ukryty głęboko w lesie na rzece Świślinie zbiornik, którego brzegi stromo opadają w stronę koryta. Częste wahania wody wymagają świetnej techniki i "czytania" akwenu.'],
            ['name' => 'Zalew Cedzyna', 'city' => 'Cedzyna', 'region' => 'świętokrzyskie', 'type' => 'zalew', 'lat' => 50.8712, 'lng' => 20.7389, 'fish' => ['Szczupak' => 35, 'Karp' => 25, 'Leszcz' => 20, 'Lin' => 20], 'desc' => 'Woda leżąca dosłownie na obrzeżach samych Kielc. Płytka i mocno nagrzewająca się latem. Mimo presji mieszczuchów, regularnie obdarza wędkarzy ładnymi zdobyczami lina i karpia.'],
            ['name' => 'Jezioro Brody Iłżeckie', 'city' => 'Brody', 'region' => 'świętokrzyskie', 'type' => 'jezioro', 'lat' => 51.0156, 'lng' => 21.1712, 'fish' => ['Sandacz' => 30, 'Szczupak' => 30, 'Leszcz' => 25, 'Płoć' => 15], 'desc' => 'Zalew na rzece Kamiennej ze starymi palami na dnie i bardzo zróżnicowaną głębokością. Znany na całą okolicę jako miejsce pewnych jesiennych brań szczupakowych z opadu.'],

            // Warmińsko-Mazurskie
            ['name' => 'Jezioro Śniardwy', 'city' => 'Nowe Guty', 'region' => 'warmińsko-mazurskie', 'type' => 'jezioro', 'lat' => 53.7589, 'lng' => 21.7345, 'fish' => ['Szczupak' => 35, 'Sandacz' => 25, 'Okoń' => 20, 'Węgorz' => 20], 'desc' => 'Zwane Mazurskim Morzem, największe jezioro Polski. Kapryśne wiatry, niebezpieczne, tuż podwodne głazy narzutowe, a w toni ryby, o których krążą prawdziwe legendy między wędkarzami.'],
            ['name' => 'Jezioro Mamry', 'city' => 'Węgorzewo', 'region' => 'warmińsko-mazurskie', 'type' => 'jezioro', 'lat' => 54.1812, 'lng' => 21.7089, 'fish' => ['Szczupak' => 40, 'Węgorz' => 25, 'Leszcz' => 20, 'Okoń' => 15], 'desc' => 'Drugi co do wielkości akwen z archipelagiem wysp i poniemieckimi bunkrami na brzegach. Krystaliczna woda pozwala łowić "na upatrzonego" za wielkimi ławicami okoni.'],
            ['name' => 'Jezioro Jeziorak', 'city' => 'Iława', 'region' => 'warmińsko-mazurskie', 'type' => 'jezioro', 'lat' => 53.6856, 'lng' => 19.5812, 'fish' => ['Sandacz' => 30, 'Szczupak' => 30, 'Węgorz' => 20, 'Leszcz' => 20], 'desc' => 'Najdłuższe jezioro w Polsce. Ciągnąca się kilometrami rynna to raj dla wodniaków i wędkarzy. Rozliczne trzcinowiska są wprost wymarzonym siedliskiem linów, szczupaków i wzdręg.'],
            ['name' => 'Rzeka Łyna (Olsztyn)', 'city' => 'Olsztyn', 'region' => 'warmińsko-mazurskie', 'type' => 'rzeka', 'lat' => 53.7712, 'lng' => 20.4834, 'fish' => ['Kleń' => 35, 'Pstrąg potokowy' => 30, 'Brzana' => 20, 'Jaź' => 15], 'desc' => 'Przepływająca przez olsztyńskie parki, niezwykle zmienna Łyna na każdym kilometrze wygląda inaczej. Odcinek miejski jest wspaniałym terenem na obławianie głębokich dołów pod klenie.'],

            // Wielkopolskie
            ['name' => 'Rzeka Warta (Poznań)', 'city' => 'Poznań', 'region' => 'wielkopolskie', 'type' => 'rzeka', 'lat' => 52.4064, 'lng' => 16.9252, 'fish' => ['Boleń' => 25, 'Kleń' => 25, 'Sandacz' => 25, 'Sum' => 25], 'desc' => 'Poznański odcinek Warty. W ciągu dnia to rekreacyjne serce miasta, a o świcie tutejsze warkocze i przelewy ożywają za sprawą drapieżników uderzających głośno w drobnicę.'],
            ['name' => 'Jezioro Powidzkie', 'city' => 'Powidz', 'region' => 'wielkopolskie', 'type' => 'jezioro', 'lat' => 52.4212, 'lng' => 17.9456, 'fish' => ['Szczupak' => 35, 'Sieja' => 25, 'Lin' => 20, 'Okoń' => 20], 'desc' => 'Jezioro sielawowe o niewiarygodnej widoczności sięgającej nawet kilku metrów, przypominające lagunę. Ulubione miejsce płetwonurków, gdzie każdy szczegół dna widać jak na dłoni.'],
            ['name' => 'Jezioro Kierskie', 'city' => 'Poznań', 'region' => 'wielkopolskie', 'type' => 'jezioro', 'lat' => 52.4656, 'lng' => 16.7812, 'fish' => ['Sandacz' => 30, 'Węgorz' => 25, 'Leszcz' => 25, 'Szczupak' => 20], 'desc' => 'Aktywny, sportowy akwen w zachodniej części Poznania, z potężną flotą jachtów. Podwodny opad stromej rynny kryje potężne pasiaki, których łowienie wymaga cichych, wieczornych podejść.'],
            ['name' => 'Jezioro Lednica', 'city' => 'Lednogóra', 'region' => 'wielkopolskie', 'type' => 'jezioro', 'lat' => 52.5456, 'lng' => 17.3712, 'fish' => ['Szczupak' => 35, 'Leszcz' => 30, 'Lin' => 20, 'Płoć' => 15], 'desc' => 'Historyczna wyspa i symboliczna Ryba nad wodą to wizytówka Lednicy. Jezioro o podłużnym rynnowym charakterze, znane z wędkowania spławikowego z łodzi i ciszy na dalekich plosach.'],

            // Zachodniopomorskie
            ['name' => 'Jezioro Drawsko', 'city' => 'Czaplinek', 'region' => 'zachodniopomorskie', 'type' => 'jezioro', 'lat' => 53.5812, 'lng' => 16.1856, 'fish' => ['Szczupak' => 35, 'Okoń' => 25, 'Sieja' => 20, 'Leszcz' => 20], 'desc' => 'Głęboki, polodowcowy gigant Pojezierza Drawskiego o niespotykanym kształcie pajęczyny. Pełno tu wysp i ekstremalnie głębokich rynien (nawet 80 metrów!) pełnych prastarych drapieżników.'],
            ['name' => 'Jezioro Miedwie', 'city' => 'Stargard', 'region' => 'zachodniopomorskie', 'type' => 'jezioro', 'lat' => 53.2856, 'lng' => 14.8812, 'fish' => ['Sandacz' => 35, 'Sieja' => 25, 'Węgorz' => 20, 'Leszcz' => 20], 'desc' => 'Dawna baza szkoleniowa Kriegsmarine, po której do dziś zostały na dnie pozostałości betonowych konstrukcji. To trudne, wymagające jezioro słynące z wietrznej i kapryśnej aury.'],
            ['name' => 'Rzeka Odra (Szczecin)', 'city' => 'Szczecin', 'region' => 'zachodniopomorskie', 'type' => 'rzeka', 'lat' => 53.4289, 'lng' => 14.5532, 'fish' => ['Sandacz' => 35, 'Sum' => 25, 'Boleń' => 20, 'Węgorz' => 20], 'desc' => 'Okolice szczecińskiego portu to industrialny plener dla jesiennych łowców grubych sandaczy. Woda toczy się tu leniwie i miesza z Bałtykiem, co owocuje spektakularnymi zjawiskami na echosondzie.'],
            ['name' => 'Jezioro Jamno', 'city' => 'Mielno', 'region' => 'zachodniopomorskie', 'type' => 'jezioro', 'lat' => 54.2656, 'lng' => 16.1412, 'fish' => ['Sandacz' => 30, 'Węgorz' => 30, 'Szczupak' => 20, 'Leszcz' => 20], 'desc' => 'Płytki przymorski akwen oddzielony od Bałtyku jedynie mierzeją. Miejscami woda jest słonawa z powodu morskich wlewek przez kanał Jamieński. Popularne łowisko lodu z uwagi na płytką toń.'],
        ];

        // -------------------------------------------------------------------------
        // 5. ZAPIS DO BAZY DANYCH 
        // -------------------------------------------------------------------------
        foreach ($spots as $spotData) {
            // Wyodrębnienie danych o składzie rybostanu
            $spotFish = $spotData['fish'];
            unset($spotData['fish']);

            // Zmiana klucza 'desc' na 'description' w celu dopasowania do schematu tabeli łowisk
            $spotData['description'] = $spotData['desc'];
            unset($spotData['desc']);

            // Domyślne zdjęcie łowiska
            $spotData['main_image_url'] = 'https://images.unsplash.com/photo-1506140502283-0571daae90d0?w=800&q=80';

            // Utworzenie rekordu łowiska
            $fishingSpot = FishingSpot::create($spotData);

            // PRZYPISYWANIE GATUNKÓW RYB
            $dominantFish = null;
            $maxPercent = 0;
            foreach ($spotFish as $fishName => $percent) {
                // Szukanie dominującego gatunku w danym łowisku do spersonalizowania opinii
                if ($percent > $maxPercent) {
                    $maxPercent = $percent;
                    $dominantFish = $fishName;
                }
                $fishId = $speciesMap[$fishName];
                // Dołączenie rekordu do tabeli łącznikowej z procentem zarybienia
                $fishingSpot->fish_species()->attach($fishId, ['percentage' => $percent]);
            }

            // UDOGODNIENIA (Losowe przypisanie od 3 do 6 udogodnień)
            $randomAmenities = (array) array_rand(array_flip($amenityIds), rand(3, 6));
            $fishingSpot->amenities()->attach($randomAmenities);

            // GENEROWANIE LOSOWYCH OPINII (od 1 do 3 recenzji na łowisko)
            $shuffledAnglers = collect($anglers)->shuffle();
            $reviewers = $shuffledAnglers->take(rand(1, 3));

            foreach ($reviewers as $reviewer) {
                // Wybór losowego szablonu opinii
                $randomTemplate = $reviewTemplates[array_rand($reviewTemplates)];

                // Zmiana znaczników [:fish] i [:spot] na konkretne wartości dla tego łowiska
                $personalizedComment = str_replace(
                    ['[:fish]', '[:spot]'],
                    [$dominantFish, $fishingSpot->name],
                    $randomTemplate
                );

                Review::create([
                    'user_id' => $reviewer->id,
                    'fishing_spot_id' => $fishingSpot->id,
                    'rating' => rand(3, 5),
                    'comment' => $personalizedComment,
                ]);
            }

            // REKORDY RYB
            $recordAngler = $shuffledAnglers->random();
            FishRecord::create([
                'user_id' => $recordAngler->id,
                'fishing_spot_id' => $fishingSpot->id,
                'fish_name' => $dominantFish,
                'weight' => rand(20, 150) / 10,
                'length' => rand(45, 110),       
                'photo_url' => 'https://images.unsplash.com/photo-1544983226-9d32b575eaee?w=800&q=80',
                'status' => 'approved',          
                'caught_at' => Carbon::now()->subDays(rand(1, 30)), 
            ]);
        }
    }
}
