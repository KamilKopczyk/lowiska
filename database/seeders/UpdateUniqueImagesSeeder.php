<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishingSpot;
use App\Models\FishRecord;

class UpdateUniqueImagesSeeder extends Seeder
{
    /**
     * Uruchomienie seeder-a aktualizującego zdjęcia łowisk i rekordów.
     */
    public function run()
    {
        // Aktualizacja zdjęć łowisk
        $spots = FishingSpot::all();
        $this->command->info("Rozpoczynam pobieranie zdjęć dla {$spots->count()} łowisk...");

        $updatedSpotsCount = 0;
        foreach ($spots as $spot) {
            $realPhoto = $this->getWikipediaImage($spot->name);

            if ($realPhoto) {
                $spot->main_image_url = $realPhoto;
                $spot->save();
                $this->command->line(" -> [Łowisko] {$spot->name}: Zapisano zdjęcie ({$realPhoto})");
                $updatedSpotsCount++;
            } else {
                // Zapasowy krajobraz w przypadku braku zdjęcia
                $spot->main_image_url = "https://loremflickr.com/800/600/lake,river,poland/all?lock=" . $spot->id;
                $spot->save();
                $this->command->line(" -> [Łowisko] {$spot->name}: Ustawiono zapasowy krajobraz.");
            }
        }
        $this->command->info("Zakończono aktualizację łowisk. Sukces: {$updatedSpotsCount}");

        // Aktualizacja zdjęć rekordów
        $records = FishRecord::all();
        $this->command->info("Rozpoczynam pobieranie zdjęć dla {$records->count()} rekordów ryb...");

        $updatedRecordsCount = 0;
        foreach ($records as $record) {
            // Pominięcie zdjęć wgranych przez użytkowników
            if (str_starts_with($record->photo_url, '/uploads')) {
                $this->command->line(" -> [Rekord ID {$record->id}] Pomijam (własne zdjęcie użytkownika).");
                continue;
            }

            $realFishPhoto = $this->getWikipediaImage($record->fish_name);

            if ($realFishPhoto) {
                $record->photo_url = $realFishPhoto;
                $record->save();
                $this->command->line(" -> [Rekord] {$record->fish_name}: Zapisano zdjęcie ({$realFishPhoto})");
                $updatedRecordsCount++;
            } else {
                $record->photo_url = "https://loremflickr.com/800/600/fish,fishing/all?lock=" . $record->id;
                $record->save();
                $this->command->line(" -> [Rekord] {$record->fish_name}: Ustawiono zapasowe zdjęcie.");
            }
        }
        $this->command->info("Zakończono aktualizację rekordów. Sukces: {$updatedRecordsCount}");
    }

    /**
     * Pobiera adres URL zdjęcia z Wikipedii dla podanej frazy za pomocą API MediaWiki.
     * Algorytm wyszukuje najbardziej pasujący artykuł encyklopedyczny, a następnie
     * wyciąga z niego bezpośredni link do głównej miniatury graficznej (PageImages).
     */
    private function getWikipediaImage($searchTerm)
    {
        // Krok 1: Oczyszczanie frazy za pomocą Wyrażeń Regularnych.
        // Usuwa wszelkie dopiski w nawiasach okrągłych (np. "Jezioro Solińskie (Solina)" -> "Jezioro Solińskie").
        $searchTerm = preg_replace('/\s*\(.*?\)\s*/', '', $searchTerm);

        // Krok 2: Normalizacja nazwy rzeki.
        // Usuwa prefiks "rzeka " (np. "Rzeka Wisła" -> "Wisła") dla lepszych wyników wyszukiwania w wikipedi.
        if (str_starts_with(strtolower($searchTerm), 'rzeka ')) {
            $searchTerm = substr($searchTerm, 6);
        }

        // Krok 3: Budowanie zapytania HTTP do API Wikipedii.
        // - generator=search: uruchamia pełnotekstową wyszukiwarkę artykułów
        // - gsrlimit=1: pobiera wyłącznie pierwszy, najbardziej trafny artykuł
        // - prop=pageimages: nakazuje dołączyć metadane o głównej grafice artykułu
        // - pithumbsize=800: wymusza na serwerach Wikimedia wyrenderowanie i wysłanie wysokiej jakości miniatury (szerokość 800px)
        $url = "https://pl.wikipedia.org/w/api.php?action=query&generator=search&gsrsearch=" . urlencode($searchTerm) . "&gsrlimit=1&prop=pageimages&piprop=thumbnail&pithumbsize=800&format=json";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Wikipedia wymaga unikalnego nagłówka User-Agent (tzw. "Api-User-Agent"),
            // bez którego zapytania mogą zostać zablokowane lub spowolnione przez WMF (Rate Limiting).
            curl_setopt($ch, CURLOPT_USERAGENT, 'WedkarskiRadarBot/1.0 (kontakt@radarwedkarski.pl)');
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);

            // Obejście SSL na lokalnym środowisku programistycznym (wyłączenie weryfikacji certyfikatów HTTPS)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);

            if ($response === false) {
                curl_close($ch);
                return null;
            }

            curl_close($ch);

            if ($response) {
                $data = json_decode($response, true);

                // Krok 4: Bezpieczne parsowanie zagnieżdżonego drzewa JSON.
                // Ponieważ Wikipedia grupuje wyniki według dynamicznych ID podstron (np. query -> pages -> [12345]),
                // używam funkcji reset() do wyciągnięcia pierwszego elementu tablicy asocjacyjnej bez znajomości klucza ID.
                if (isset($data['query']['pages'])) {
                    $pages = $data['query']['pages'];
                    $firstPage = reset($pages);
                    if (isset($firstPage['thumbnail']['source'])) {
                        return $firstPage['thumbnail']['source']; // Bezpośredni link do miniatury z serwerów Wikimedia
                    }
                }
            }
        } catch (\Exception $e) {
            // Cicha obsługa ewentualnych wyjątków sieciowych
        }

        return null;
    }
}
