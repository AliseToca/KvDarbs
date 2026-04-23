<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('product_categories')->pluck('id', 'name');
        $measurements = DB::table('measurement_types')->pluck('id', 'name');

        $svars = $measurements['svars'];
        $tilpums = $measurements['tilpums'];
        $skaits = $measurements['skaits'];

        $gala = $categories['Gaļa un zivis'];
        $piens = $categories['Piena produkti un olas'];
        $augldarz = $categories['Augļi un dārzeņi'];
        $citi = $categories['Citi krājumi'];

        $products = [
            // --- Gaļa un zivis ---
            ['name' => 'Vistas fileja', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Vistas stilbiņi', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Liellopa malā gaļa', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Liellopa steiks', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Cūkgaļas karbonāde', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Cūkgaļas ribiņas', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Bekons', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Šķiņķis', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Jēra gaļa', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Tītara fileja', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Lasis (fileja)', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Forele', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Menča', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Garneles', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Tuncis (konservs)', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Siļķe', 'product_category_id' => $gala, 'measurement_type_id' => $svars],
            ['name' => 'Kalmāri', 'product_category_id' => $gala, 'measurement_type_id' => $svars],

            // --- Piena produkti un olas ---
            ['name' => 'Piens (3,5%)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Piens (2,5%)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Piens (0,5%)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Sojas piens', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Auzu piens', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Mandeļu piens', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Kokosriekstu dzēriens', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Rīsu piens', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Saldais krējums (35%)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Saldais krējums (20%)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Skābais krējums', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Sviests', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Vegāniskais sviests', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Auzu krējums (vegāniskais)', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Biezpiens', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Biezpiens (zema tauku satura)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Jogurts (dabīgais)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Grieķu jogurts', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Sojas jogurts (vegāniskais)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Kokosriekstu jogurts (vegān.)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Kefīrs', 'product_category_id' => $piens, 'measurement_type_id' => $tilpums],
            ['name' => 'Kondensētais piens', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Parmezāns', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Čedaras siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Mocarella', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Fetas siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Rīvēts siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Brie siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Guda siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Maskarponē', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Krēmsiers (Ricotta)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Krēmsiers (Philadelphia tips)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Vegāniskais čedaras siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Vegāniskais rīvēts siers', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Tofu (dabīgais)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Tofu (kūpināts)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Tofu (zīdainais)', 'product_category_id' => $piens, 'measurement_type_id' => $svars],
            ['name' => 'Vistas ola', 'product_category_id' => $piens, 'measurement_type_id' => $skaits],
            ['name' => 'Paipalas ola', 'product_category_id' => $piens, 'measurement_type_id' => $skaits],

            // --- Augļi un dārzeņi ---
            ['name' => 'Sīpols', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ķiploks', 'product_category_id' => $augldarz, 'measurement_type_id' => $skaits],
            ['name' => 'Burkāns', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Kartupelis', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Tomāts', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Gurķis', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Paprika (sarkanā)', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Paprika (zaļā)', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Brokoļi', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ziedkāposts', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Kāposts', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ķīnas kāposts', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ķirbis', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Cukini', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Baklažāns', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Spināti', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Selerija (sakne)', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Poru sīpols', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Redīss', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Biete', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Kukurūza (konservēta)', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Tomāti (konservēti)', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ķiršu tomāti', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Šampinjoni', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Portobello sēnes', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Baravikas', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Gailenes', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Bērzlapes', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Šiitake sēnes', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Ābols', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Bumbieris', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Banāns', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Apelsīns', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Citrons', 'product_category_id' => $augldarz, 'measurement_type_id' => $skaits],
            ['name' => 'Laims', 'product_category_id' => $augldarz, 'measurement_type_id' => $skaits],
            ['name' => 'Zemene', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Mellene', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],
            ['name' => 'Avenes', 'product_category_id' => $augldarz, 'measurement_type_id' => $svars],

            // --- Citi krājumi ---
            // Garšvielas
            ['name' => 'Sāls', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Melnie pipari (malts)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Melnie pipari (veseli)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Paprikas pulveris', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Ķimenes', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Kurkuma', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Karija pulveris', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Oregano (žāvēts)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Baziliks (žāvēts)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Timiāns', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Rozmarīns', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Lauru lapas', 'product_category_id' => $citi, 'measurement_type_id' => $skaits],
            ['name' => 'Ingvers (malts)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Ingvers (svaigs)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Kanēlis (malts)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Muskatrieksts', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Čili pipars (svaigs)', 'product_category_id' => $citi, 'measurement_type_id' => $skaits],
            ['name' => 'Čili pārslas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            // Maize un milti
            ['name' => 'Kviešu milti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Rudzu milti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Kukurūzas milti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Pankūku maisījums', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Maizes raugs (sausais)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Baltmaize', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Rudzu maize', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Rīvmaize', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Lavašs', 'product_category_id' => $citi, 'measurement_type_id' => $skaits],
            ['name' => 'Tortilja', 'product_category_id' => $citi, 'measurement_type_id' => $skaits],
            // Eļļas un tauki
            ['name' => 'Olīveļļa (extra virgin)', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Saulespuķu eļļa', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Kokosriekstu eļļa', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Sezama eļļa', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Rapšu eļļa', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Cūkgaļas tauki', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            // Graudaugi un putraimi
            ['name' => 'Baltie rīsi', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Brūnie rīsi', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Griķu putraimi', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Auzu pārslas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Mieži (pērļu)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Prosa', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Spagehetti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Penne', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Tagliatelle', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Lazanjas plāksnes', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            // Pākšaugi
            ['name' => 'Sarkanās pupiņas (konservētas)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Baltās pupiņas (konservētas)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Aunazirņi (konservēti)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Zaļie zirņi', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Zaļās lēcas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Sarkanās lēcas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Sojas pupiņas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            // Rieksti un sēklas
            ['name' => 'Valrieksti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Mandeles', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Lazdu rieksti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Zemesrieksti', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Sezama sēklas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Saulespuķu sēklas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Čia sēklas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Ķirbja sēklas', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Zaļais tahīni', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            // Konditorejas izstrādājumi
            ['name' => 'Cukurs (baltais)', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Brūnais cukurs', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Pūdercukurs', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Medus', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Kļavu sīrups', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Tumšā šokolāde', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Kakao pulveris', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Vaniļas ekstrakts', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Cepamais pulveris', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Cepamā soda', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Želatīns (loksnes)', 'product_category_id' => $citi, 'measurement_type_id' => $skaits],
            // Buljons un mērces
            ['name' => 'Vistas buljons', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Liellopu buljons', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Dārzeņu buljons', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Tomātu paste', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Sojas mērce', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Vorcestīrašīras mērce', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Balzamiko etiķis', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Vīna etiķis (baltais)', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Ābolu sidra etiķis', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Dijon sinepes', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Majonēze', 'product_category_id' => $citi, 'measurement_type_id' => $svars],
            ['name' => 'Tabasco', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
            ['name' => 'Kokosriekstu piens', 'product_category_id' => $citi, 'measurement_type_id' => $tilpums],
        ];

        DB::table('products')->insert($products);
    }
}
