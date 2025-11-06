<?php

namespace App\Data;

class TanzaniaRegions
{
    /**
     * Get all Tanzania regions with their districts
     *
     * @return array
     */
    public static function getRegionsWithDistricts(): array
    {
        return [
            'Arusha' => ['Arusha DC', 'Arusha TC', 'Karatu', 'Longido', 'Meru', 'Monduli', 'Ngorongoro'],
            'Dar es Salaam' => ['Ilala', 'Kinondoni', 'Temeke', 'Ubungo', 'Kigamboni'],
            'Dodoma' => ['Bahi', 'Chamwino', 'Chemba', 'Dodoma DC', 'Dodoma TC', 'Kondoa', 'Kongwa', 'Mpwapwa'],
            'Geita' => ['Bukombe', 'Chato', 'Geita TC', 'Geita DC', 'Mbogwe', 'Nyang\'hwale'],
            'Iringa' => ['Iringa DC', 'Iringa MC', 'Kilolo', 'Mafinga TC', 'Mufindi'],
            'Kagera' => ['Biharamulo', 'Bukoba DC', 'Bukoba MC', 'Karagwe', 'Kyerwa', 'Missenyi', 'Muleba', 'Ngara'],
            'Katavi' => ['Mlele', 'Mpanda DC', 'Mpanda TC', 'Tanganyika'],
            'Kigoma' => ['Buhigwe', 'Kakonko', 'Kasulu DC', 'Kasulu TC', 'Kibondo', 'Kigoma DC', 'Kigoma-Ujiji MC', 'Uvinza'],
            'Kilimanjaro' => ['Hai', 'Moshi DC', 'Moshi MC', 'Mwanga', 'Rombo', 'Same', 'Siha'],
            'Lindi' => ['Kilwa', 'Lindi DC', 'Lindi MC', 'Liwale', 'Nachingwea', 'Ruangwa'],
            'Manyara' => ['Babati DC', 'Babati TC', 'Hanang', 'Kiteto', 'Mbulu', 'Simanjiro'],
            'Mara' => ['Bunda', 'Butiama', 'Musoma DC', 'Musoma MC', 'Rorya', 'Serengeti', 'Tarime'],
            'Mbeya' => ['Busokelo', 'Chunya', 'Kyela', 'Mbarali', 'Mbeya DC', 'Mbeya CC', 'Rungwe'],
            'Morogoro' => ['Gairo', 'Kilombero', 'Kilosa', 'Malinyi', 'Morogoro DC', 'Morogoro MC', 'Mvomero', 'Ulanga'],
            'Mtwara' => ['Masasi DC', 'Masasi TC', 'Mtwara DC', 'Mtwara MC', 'Nanyumbu', 'Newala', 'Tandahimba'],
            'Mwanza' => ['Ilemela', 'Kwimba', 'Magu', 'Misungwi', 'Nyamagana', 'Sengerema', 'Ukerewe'],
            'Njombe' => ['Ludewa', 'Makambako TC', 'Makete', 'Njombe DC', 'Njombe TC', 'Wanging\'ombe'],
            'Pwani' => ['Bagamoyo', 'Kibaha DC', 'Kibaha TC', 'Kisarawe', 'Mafia', 'Mkuranga', 'Rufiji'],
            'Rukwa' => ['Kalambo', 'Nkasi', 'Sumbawanga DC', 'Sumbawanga MC'],
            'Ruvuma' => ['Madaba', 'Mbinga', 'Namtumbo', 'Nyasa', 'Songea DC', 'Songea MC', 'Tunduru'],
            'Shinyanga' => ['Kahama TC', 'Kahama DC', 'Kishapu', 'Shinyanga DC', 'Shinyanga MC', 'Ushetu'],
            'Simiyu' => ['Bariadi', 'Busega', 'Itilima', 'Maswa', 'Meatu'],
            'Singida' => ['Ikungi', 'Iramba', 'Manyoni', 'Mkalama', 'Singida DC', 'Singida MC'],
            'Songwe' => ['Ileje', 'Mbozi', 'Momba', 'Songwe', 'Tunduma'],
            'Tabora' => ['Igunga', 'Kaliua', 'Nzega DC', 'Nzega TC', 'Sikonge', 'Tabora MC', 'Urambo', 'Uyui'],
            'Tanga' => ['Bumbuli', 'Handeni DC', 'Handeni TC', 'Kilindi', 'Korogwe DC', 'Korogwe TC', 'Lushoto', 'Mkinga', 'Muheza', 'Pangani', 'Tanga CC'],
            'Zanzibar Kaskazini' => ['Kaskazini A', 'Kaskazini B'],
            'Zanzibar Kusini' => ['Kati', 'Kusini'],
            'Zanzibar Mjini Magharibi' => ['Magharibi', 'Mjini'],
            'Pemba Kaskazini' => ['Micheweni', 'Wete'],
            'Pemba Kusini' => ['Chake Chake', 'Mkoani'],
        ];
    }

    /**
     * Get only region names
     *
     * @return array
     */
    public static function getRegions(): array
    {
        return array_keys(self::getRegionsWithDistricts());
    }

    /**
     * Get districts for a specific region
     *
     * @param string $region
     * @return array
     */
    public static function getDistrictsByRegion(string $region): array
    {
        $regions = self::getRegionsWithDistricts();
        return $regions[$region] ?? [];
    }
}
