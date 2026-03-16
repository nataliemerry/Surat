<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Daftar pegawai BPS Kab. Magelang.
     * Kolom 'nip' diisi dengan 18 digit NIP masing-masing pegawai.
     * Format NIP: YYYYMMDD YYMMQQ G SSS (18 digit tanpa spasi di DB).
     * Contoh: '199407262024212004' → akan diformat 19940726 202421 2 004 di Excel.
     */
    public function run(): void
    {
        $pegawais = [
            ['nama' => 'Kus Haryono, S.Si., M.Si', 'nip' => 197203201994121001],
            ['nama' => 'Ahmad Taufiq, SST, M.M.', 'nip' => 197803181999121001],
            ['nama' => 'Akhmad Wahyudin', 'nip' => 196804272009011004],
            ['nama' => 'Alfitri Suryaningsih, S.Si., MA', 'nip' => 197410051995122001],
            ['nama' => 'Ali Gufront, B.St.', 'nip' => 197209201995121001],
            ['nama' => 'Andry Sulistyo, S.E.', 'nip' => 197701152001121002],
            ['nama' => 'Ari Nurvitasari, SST', 'nip' => 199308232017012001],
            ['nama' => 'Bagus Budiarta', 'nip' => 197801242006041012],
            ['nama' => 'Basuki Abdullah', 'nip' => 196903302009011004],
            ['nama' => 'Clara Sherly Rifera Putri, S.ST.', 'nip' => 198612292009122003],
            ['nama' => 'Diana Larasati, S.Si., M.M.', 'nip' => 197408201997122001],
            ['nama' => 'Eko Hermawati Agustin Setiyaningrum, SST, M.E.', 'nip' => 198308112004122001],
            ['nama' => 'Eko Indarmawan, A.Md.', 'nip' => 198508102006041002],
            ['nama' => 'Etania Harum Yonanda, SST, M.Ec.Dev.', 'nip' => 198609252009022004],
            ['nama' => 'Fardiana, S.P.', 'nip' => 196807201994012001],
            ['nama' => 'Fetia Nursih Handayani, S.P.', 'nip' => 198502162011012011],
            ['nama' => 'Fitri Hapsari, A.Md.', 'nip' => 198012022002122008],
            ['nama' => 'Handy Wida Suryanto', 'nip' => 197403232007011001],
            ['nama' => 'Heny Widiastuti, SST, M.Sc.', 'nip' => 198510152008012002],
            ['nama' => 'Hevi Dwi Susanti, S.E.', 'nip' => 198609182006042001],
            ['nama' => 'J. Enggar Catur Septiono', 'nip' => 197609112002121006],
            ['nama' => 'Jadhi Kurniawan HS', 'nip' => 197910082007011003],
            ['nama' => 'Joko Permono', 'nip' => 197005021992021001],
            ['nama' => 'Joko Prasetiyo, SST, M.Si.', 'nip' => 198004242003121007],
            ['nama' => 'Khairunnisa Dewi Maharani, S.Tr.Stat.', 'nip' => 199701302019122001],
            ['nama' => 'Lucky Kurniati, SST', 'nip' => 197910192002122002],
            ['nama' => 'Lutfiah Adela Arzie, SST', 'nip' => 197705111999122001],
            ['nama' => 'Mevi Purbiyanto, A.Md.', 'nip' => 198201022007011001],
            ['nama' => 'Muhamad Muhklasin', 'nip' => 197010302009011003],
            ['nama' => 'Munfiati Lestari, S.Si.', 'nip' => 197504081996122001],
            ['nama' => 'Nanda Buanita Addien', 'nip' => 198704132008012001],
            ['nama' => 'Nur Samsul Bichan', 'nip' => 197812272002121003],
            ['nama' => 'Okder Insantri', 'nip' => 197410181994012001],
            ['nama' => 'Ossy Sanityasa Rahajeng, S.Tr.Stat.', 'nip' => 199809172022012001],
            ['nama' => 'Ratih Kusuma Dewi, SST, M.Si.', 'nip' => 198805042010122001],
            ['nama' => 'Restu Asih Trianto, SST, M.M.', 'nip' => 198305252006021003],
            ['nama' => 'Retno Puji Kartinindyah, A.Md.', 'nip' => 198804132010032001],
            ['nama' => 'Rika Dwi Apriliyanti', 'nip' => 198704092008012002],
            ['nama' => 'Rina Arifatul Khoridah, SST', 'nip' => 199209092014102001],
            ['nama' => 'Sadjana Yoga Hidayat, S.Si.', 'nip' => 198210102009021014],
            ['nama' => 'Septania Ayu Wardhani, SST', 'nip' => 198509022008012003],
            ['nama' => 'Setyo Dwi Kuncoro', 'nip' => 197712282007011002],
            ['nama' => 'Sofa Nur Khamama, SST', 'nip' => 198805152010122004],
            ['nama' => 'Suroso, S.E.', 'nip' => 197604132006041016],
            ['nama' => 'Tri Murni Hati Khasanah, SST', 'nip' => 197801191999122001],
            ['nama' => "Verliya Gadis Rhoma'idah, SST", 'nip' => 198909122012112002],
            ['nama' => 'Wahyu Herry Wibowo, SST, M.E.', 'nip' => 198112222004121003],
            ['nama' => 'Yuli Cahyono, S.M.', 'nip' => 197807302007011001],
            ['nama' => 'Yuliana Himmatul Ulya, A.Md.', 'nip' => 199407262024212004],
            ['nama' => 'Septi Nurhayati', 'nip' => 198509222025212027],
            ['nama' => 'Muhamad Azis', 'nip' => 199004022025211062],
            ['nama' => 'Muttaqin', 'nip' => 198709302025211040],
            ['nama' => 'Misbachul Munir', 'nip' => 197604252025211014],
            ['nama' => 'Rahayu Rachmawati, SST, M.Si.', 'nip' => 197805262000122001],
        ];

        foreach ($pegawais as $data) {
            Pegawai::firstOrCreate(['nama' => $data['nama']], ['nip' => $data['nip']]);
        }
    }
}
