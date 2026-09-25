<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffSeeder;
use Database\Seeders\UnitKerjaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffSeederTest extends TestCase
{
    use RefreshDatabase;

    private const STORAGE_QUOTA = 107_374_182_400;

    private const STAFF_BY_UNIT = [
        'LPSE' => [
            ['DODI HENDRAWAN, ST., MEP', '197006291998031004'],
            ['Ir. FABIL HUDA', '196903261998031003'],
            ['MUHAMMAD YUSRON, ST., MT', '197701262006041010'],
            ['YAN SOPA, SE., MM', '198501202006041005'],
            ['SRI RANTI, S.Sos., MM', '197702111998032001'],
            ['LIA OKTIARA, SE., MM', '198010292008042001'],
            ['RANI ARSIKA SUBING, SE., MM', '198408122009022007'],
            ['ROSIDI, S.IP', '197012182000031003'],
            ['SARIFUDDIN, SE', '197307141998031004'],
            ['FIRDALIA', '197904072003122004'],
            ['ACHMAD ADENAN, S. M', '197107182007011005'],
            ['RIZALDO ABDULRACHMAN, S. Kom', '199907162025041002'],
            ['RIZKY YULIANTO, ST', '200007132025041001'],
            ['IRFANI MAHARANI, S. Kom', '199510192025042002'],
            ['NORI HERMAWAN, S. AP', '198502282010011006'],
            ['M. TAKIM', '197409292007011003'],
            ['RAHMA MARLIANA PUTRI, SE', '199103262025212013'],
            ['JUNAIDI', '198602272025211021'],
        ],
        'JF' => [
            ['Drs. IRHANNA, MM', '196710201987011003'],
            ['HERMALIA, SP., MM', '197501092000032004'],
            ['REFANSIUS MANGAMAN S. SE', '197008301995031001'],
            ['ADIKA RATU, S.Sos., MM', '197208221993032003'],
            ['BUDHI ANSORI, SH', '197609152002121006'],
            ['HERY WISNU HARYATNO, ST., MM', '198002282006041004'],
            ['NOVI HANDAYANI, ST., MT', '198011202006042011'],
            ['EKO AGUST PRIYONO, ST', '198008312003121002'],
            ['HARJANTO SETIAJI, ST., MM', '197408212002121003'],
            ['ANDRI HARDATAMA, SE., MM', '198101022010011013'],
            ['HERLI ANDRIANI, SH, MM', '197401182007012008'],
            ['HERNELI DIANAWATI, ST', '197304052005022005'],
            ['IHWAN NUDIN, S.Kep', '198204242009021003'],
            ['MAS MUHAMMAD ASRI M.S.Sos', '197112292008011007'],
            ['IVAN YULINDO, SE., MM', '197707192011011001'],
            ['LAZNAWATI, SE., MM', '197009031992032004'],
            ['AHMAD HERNAWAN, S.P', '197109172005011007'],
            ['SUBAROKAH SAFARI, SST', '197602212005011004'],
            ['HARISON YUSA, ST., MT', '198402082005011002'],
            ['AGUSTIADI, ST., MT', '197608022007011021'],
            ['ADE YURIZA, SH', '199007202015031003'],
        ],
        'PSDM' => [
            ['WAYAN PURWANAJATA, S.P', '197504271999021001'],
            ['YASIR HERYANTO, SE., MM', '197810292009021002'],
            ['SHERLI YESSI, ST., MT', '197711132003122002'],
            ['YULIANA USMAN, SH., MM', '198407272008042004'],
            ['RACHMAT, SE., MM', '197601232011011003'],
            ['FAULIANI ARIANI, A. Md', '197506112008012016'],
            ['ZAQI ILMAN JIWANDONO, S. Sos., M. Si', '199012242020121006'],
            ['INSAF SURAHMAN, SH', '197507082008011009'],
            ['ANDI ARIYANDI, S. Kom', '199803152025041001'],
            ['ARINI SUSANTO, S. Kom', '199304022025042001'],
            ['REZA SAPUTRAAZMI, S. Kom', '199309252025041004'],
            ['ERINDA ALIN KURNIA, S. Akt', '199811132025212012'],
            ['MUHAMMAD TUHRO', '198406032025211024'],
        ],
        'PBJ' => [
            ['BUDI SETIAWAN, S. Kom., MM', '197205312002121006'],
            ['IDAWATI, SH., MM', '197403231995032001'],
            ['ANDY DERMAWAN, ST., MM', '197508022006041002'],
            ['AGUS SETIAWAN, ST., MM', '197608312008041001'],
            ['LISTIYANI, S.IP., MM', '197406101994022002'],
            ['AGUS INDRA SURI, SE', '196908081990031009'],
            ['HESTI LARA RADIANI, SE', '198305042010012012'],
            ['SOFYANI, S. Kom', '199405032025041004'],
            ['SINTA WIDYANISA, S. Kom', '199404232025042001'],
            ['WASRONI', '198304162025211008'],
        ],
    ];

    public function test_staff_seeder_creates_grouped_users_idempotently(): void
    {
        $this->seed([RoleSeeder::class, UnitKerjaSeeder::class, StaffSeeder::class]);

        $this->assertSeededStaff();

        $this->seed(StaffSeeder::class);

        $this->assertSeededStaff();
    }

    private function assertSeededStaff(): void
    {
        $expectedCount = array_sum(array_map('count', self::STAFF_BY_UNIT));
        $nips = [];

        foreach (self::STAFF_BY_UNIT as $unitCode => $staffMembers) {
            foreach ($staffMembers as [$name, $nip]) {
                $nips[] = $nip;
                $user = User::query()->with(['role', 'unitKerja'])->where('nip', $nip)->sole();

                $this->assertSame($name, $user->nama);
                $this->assertNull($user->email);
                $this->assertSame('user', $user->role->slug);
                $this->assertSame($unitCode, $user->unitKerja->kode);
                $this->assertTrue($user->is_active);
                $this->assertSame(self::STORAGE_QUOTA, $user->storage_quota);
                $this->assertSame(0, $user->storage_used);
                $this->assertTrue(Hash::check('password', $user->password));
            }
        }

        $this->assertSame(62, $expectedCount);
        $this->assertSame($expectedCount, User::query()->whereIn('nip', $nips)->count());
    }
}
