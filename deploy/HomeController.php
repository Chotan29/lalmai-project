<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\EmailAlerts;
use App\Models\AttendanceMaster;
use App\Models\Bed;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\FeeCollection;
use App\Models\FeeMaster;
use App\Models\Notice;
use App\Models\SalaryPay;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Web\WebMenu;
use App\Models\Web\WebPage;
use App\Models\Year;
use App\Models\Role;
use App\Traits\PurchaseVerification;
use App\Traits\StudentScopes;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ViewHelper;

class HomeController extends CollegeBaseController
{
    use StudentScopes;
    use PurchaseVerification;

    protected $base_route = 'dashboard';
    protected $view_path  = 'dashboard';
    protected $panel;

    public function __construct()
    {
        $this->panel = __('dashboard.name');
    }

    public function index(Request $request)
    {
        $data = [];

        // Active Year
        $year = Year::where('active_status', 1)->first();
        $activeYear = $year ? $year->title : date('Y');

        // Date filter
        $dateFilter = $this->getDateRangeFromRequest($request);
        $data['filter'] = [
            'start_date' => $dateFilter['start_date'],
            'end_date' => $dateFilter['end_date'],
            'quick_range' => $dateFilter['quick_range'],
            'activeYear' => $activeYear,
        ];

        // Notices for current user's role
        $userRoleId = auth()->user()->roles()->first()->id ?? null;
        $today = date('Y-m-d');
        $data['notice_display'] = Notice::select('last_updated_by', 'title', 'message', 'publish_date', 'end_date', 'display_group', 'status')
            ->when($userRoleId, fn($q) => $q->where('display_group', 'like', '%' . $userRoleId . '%'))
            ->where('publish_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->latest()
            ->get();

        // Indicators
        $data['studentIndicator'] = Student::where('students.status', 1)
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->join('semesters as s', 's.id', '=', 'students.semester')
            ->join('student_statuses as ss', 'ss.id', '=', 'students.academic_status')
            ->count('students.id');
        $data['staffIndicator'] = Staff::count();
        $data['onlineRegistrationIndicator'] = Student::where('students.status', 1)
            ->where('students.registration_payment_status', 'completed')
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->join('semesters as s', 's.id', '=', 'students.semester')
            ->join('student_statuses as ss', 'ss.id', '=', 'students.academic_status')
            ->count('students.id');
        $data['student_active_status'] = Student::select('students.status', DB::raw('count(students.id) as total'))
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->join('semesters as s', 's.id', '=', 'students.semester')
            ->join('student_statuses as ss', 'ss.id', '=', 'students.academic_status')
            ->groupBy('students.status')
            ->get();
        $data['academic_status_count'] = Student::select('students.academic_status', DB::raw('count(students.id) as total'))
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->join('semesters as s', 's.id', '=', 'students.semester')
            ->join('student_statuses as ss', 'ss.id', '=', 'students.academic_status')
            ->groupBy('students.academic_status')
            ->get();
        $data['staff_status'] = Staff::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();
        $data['books_status'] = Book::select('book_status', DB::raw('count(*) as total'))
            ->groupBy('book_status')->get();
        $data['bed_status'] = Bed::select('bed_status', DB::raw('count(*) as total'))
            ->groupBy('bed_status')->get();
        $data['transport_status'] = Vehicle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->get();
        $data['exams_status'] = ExamSchedule::select('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id', 'status')
            ->groupBy('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id', 'status')
            ->latest('years_id')->orderBy('months_id', 'asc')
            ->limit(10)->get()->count();

        // Faculty/Semester stats
        $data['student_faculty_active_status'] = Student::select(
            'students.faculty as faculty_ID',
            'students.semester as semester_ID',
            'f.faculty as faculty',
            's.semester as semester',
            DB::raw('count(*) as total')
        )
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->join('semesters as s', 's.id', '=', 'students.semester')
            ->where('students.status', 1)
            ->groupBy('students.faculty', 'students.semester', 'f.faculty', 's.semester')
            ->orderBy('f.faculty', 'asc')
            ->orderBy('s.semester', 'asc')
            ->get();
        $data['student_faculty_wise_active_status'] = $data['student_faculty_active_status']->groupBy('faculty');

        // Menu structure based on sidebar
        $generalSetting = (object) ['web_cms' => 1, 'front_desk' => 1, 'student_staff' => 1, 'account' => 1, 'inventory' => 1, 'library' => 1, 'attendance' => 1, 'exam' => 1, 'certificate' => 1, 'hostel' => 1, 'transport' => 1, 'assignment' => 1, 'application' => 1, 'upload_download' => 1, 'meeting' => 1, 'alert' => 1, 'academic' => 1, 'help' => 1];
        $data['menu_groups'] = $this->buildMenuGroups($generalSetting);

        return view(parent::loadDataToView($this->view_path . '.index'), compact('data'));
    }

    protected function getDateRangeFromRequest(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
        $quickRange = $request->input('quick_range', '');

        if ($quickRange) {
            $endDate = Carbon::now()->format('Y-m-d');
            switch ($quickRange) {
                case 'today':
                    $startDate = $endDate;
                    break;
                case 'week':
                    $startDate = Carbon::now()->subWeek()->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = Carbon::now()->subMonth()->format('Y-m-d');
                    break;
                case 'quarter':
                    $startDate = Carbon::now()->subQuarter()->format('Y-m-d');
                    break;
                case 'year':
                    $startDate = Carbon::now()->subYear()->format('Y-m-d');
                    break;
            }
        }

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            $startDate = $endDate;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'quick_range' => $quickRange,
        ];
    }

    protected function firstExistingRoute(array $names): ?string
    {
        foreach ($names as $name) {
            if (is_string($name) && Route::has($name)) {
                return $name;
            }
        }
        return null;
    }

    protected function darkenColor($hex, $percent = -20)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + ($percent / 100 * 255)));
        $g = max(0, min(255, $g + ($percent / 100 * 255)));
        $b = max(0, min(255, $b + ($percent / 100 * 255)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }


    protected function buildMenuGroups($generalSetting): array
    {
        $menuGroups = [
             [
                'title' => 'Dashboard',
                'icon' => 'fa-tachometer',
                'color' => '#3b82f6',
                'items' => [
                    [
                        'title' => 'Dashboard',
                        'desc' => 'Main overview',
                        'route' => ['dashboard'],
                        'icon' => 'fa-tachometer',
                    ],
                ],
            ],
            [
                'title' => 'Staff & Student',
                'icon' => 'fa-users',
                'color' => '#a855f7',
                'items' => $generalSetting->student_staff ? [
                    [
                        'title' => 'Students',
                        'desc' => 'Manage student records',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Detail', 'route' => ['student'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['student.registration'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Bulk Import', 'route' => ['student.import'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Transfer', 'route' => ['student.transfer'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Document Upload', 'route' => ['student.document'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Notes', 'route' => ['student.note'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Complete Records', 'route' => ['report.student'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Guardians',
                        'desc' => 'Manage guardian records',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Detail', 'route' => ['guardian'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['guardian.registration'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Staff',
                        'desc' => 'Manage staff records',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Detail', 'route' => ['staff'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['staff.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Bulk Import', 'route' => ['staff.import'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Document Upload', 'route' => ['staff.document'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Notes', 'route' => ['staff.note'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Designation', 'route' => ['staff.designation'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Complete Records', 'route' => ['report.staff'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],
            [
                'title' => 'Accounts',
                'icon' => 'fa-calculator',
                'color' => '#f59e0b',
                'items' => $generalSetting->account ? [
                    // ['title' => 'Dashboard', 'route' => ['account.dashboard'], 'icon' => 'fa-tachometer'],
                    [
                        'title' => 'Fees',
                        'desc' => 'Manage fees',
                        'icon' => 'fa-calculator',
                        'children' => [
                            ['title' => 'Fees Dashboard', 'route' => ['fees.dashboard'], 'icon' => 'fa-tachometer'],
                            ['title' => 'Detail', 'route' => ['account.fees'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Quick Receive', 'route' => ['account.fees.quick-receive'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Collection', 'route' => ['account.fees.collection'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Balance', 'route' => ['account.fees.balance'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Add Fee', 'route' => ['account.fees.master.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Online Payment', 'route' => ['account.fees.online-payment'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Fee Head', 'route' => ['account.fees.head'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Fee Reports',
                        'desc' => 'Financial reports',
                        'icon' => 'fa-print',
                        'children' => [
                            ['title' => 'Cash Book', 'route' => ['account.report.cash-book'], 'icon' => 'fa-rupee'],
                            ['title' => 'Fee Collection', 'route' => ['account.report.fee-collection'], 'icon' => 'fa-calculator'],
                            ['title' => 'Online Payment', 'route' => ['account.report.fee-online-payment'], 'icon' => 'fa-globe'],
                            ['title' => 'Fee Collection Head', 'route' => ['account.report.fee-collection-head'], 'icon' => 'fa-calculator'],
                            ['title' => 'Fee Balance', 'route' => ['account.report.balance-fee'], 'icon' => 'fa-calculator'],
                        ],
                    ],
                    [
                        'title' => 'Ledger Transactions',
                        'desc' => 'Manage transactions',
                        'icon' => 'fa-newspaper-o',
                        'children' => [
                            ['title' => 'Transaction', 'route' => ['account.transaction.add'], 'icon' => 'fa-plus'],
                            ['title' => 'Multi Transaction', 'route' => ['account.transaction.multi-add'], 'icon' => 'fa-plus'],
                            ['title' => 'Detail', 'route' => ['account.transaction'], 'icon' => 'fa-list'],
                            ['title' => 'Transfer', 'route' => ['account.transfer'], 'icon' => 'fa-exchange'],
                            ['title' => 'Transaction Head', 'route' => ['account.transaction-head'], 'icon' => 'fa-newspaper-o'],
                            ['title' => 'Account Groups', 'route' => ['account.transaction.account-group'], 'icon' => 'fa-newspaper-o'],
                            ['title' => 'Chart of Accounts', 'route' => ['account.transaction.account-group.chart-of-account'], 'icon' => 'fa-newspaper-o'],
                        ],
                    ],
                    [
                        'title' => 'Banking',
                        'desc' => 'Manage bank accounts',
                        'icon' => 'fa-bank',
                        'children' => [
                            ['title' => 'Manage Bank Account', 'route' => ['account.bank'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Add New Bank', 'route' => ['account.bank.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Transaction Detail', 'route' => ['account.bank-transaction'], 'icon' => 'fa-caret-right'],
                            ['title' => 'New Transaction', 'route' => ['account.bank-transaction.add'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Payroll',
                        'desc' => 'Manage payroll',
                        'icon' => 'fa-user-secret',
                        'children' => [
                            ['title' => 'Paid Detail', 'route' => ['account.payroll'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Payment', 'route' => ['account.salary.payment'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Add Payroll', 'route' => ['account.payroll.master.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Balance', 'route' => ['account.payroll.balance'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Payroll Head', 'route' => ['account.payroll.head'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Account Reports',
                        'desc' => 'Account statements',
                        'icon' => 'fa-print',
                        'children' => [
                            ['title' => 'Statement', 'route' => ['account.transaction-head.view'], 'icon' => 'fa-newspaper-o'],
                            ['title' => 'Balance', 'route' => ['account.transaction-head.balance-statement'], 'icon' => 'fa-newspaper-o'],
                            ['title' => 'Chart of Accounts', 'route' => ['account.transaction.account-group.chart-of-account'], 'icon' => 'fa-newspaper-o'],
                        ],
                    ],
                ] : [],
            ],
            [
                'title' => 'Attendance',
                'icon' => 'fa-calendar',
                'color' => '#0ea5e9',
                'items' => $generalSetting->attendance ? [
                    [
                        'title' => 'Attendance Dashboard',
                        'desc' => 'View attendance stats',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Students Dashboard', 'route' => ['attendance.dashboard.students.index'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Dashboard', 'route' => ['attendance.dashboard.staff.index'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Live Attendance',
                        'desc' => 'Record live attendance',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Students Attendance', 'route' => ['attendance.live.students'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Attendance', 'route' => ['attendance.live.staff'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    ['title' => 'Attendance Scanner', 'route' => ['attendance.scan'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Attendance Device', 'route' => ['attendance.tipsoi.dashboard'], 'icon' => 'fa-caret-right'],
                    [
                        'title' => 'Monthly Report',
                        'desc' => 'Monthly attendance reports',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Students Attendance', 'route' => ['attendance.reports.students.monthly'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Attendance', 'route' => ['attendance.reports.staff.monthly'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Card Print',
                        'desc' => 'Print attendance cards',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Students Attendance', 'route' => ['attendance.reports.individual.students'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Attendance', 'route' => ['attendance.reports.individual.staff'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],
            [
                'title' => 'Certificate',
                'icon' => 'fa-certificate',
                'color' => '#14b8a6',
                'items' => $generalSetting->certificate ? [
                    ['title' => 'Issue Certificate', 'route' => ['certificate.issue'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Attendance Certificate', 'route' => ['certificate.attendance'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Transfer Certificate', 'route' => ['certificate.transfer'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Character Certificate', 'route' => ['certificate.character'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Bonafide Certificate', 'route' => ['certificate.bonafide'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Course Completion Cer.', 'route' => ['certificate.course-completion'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Nirgam Utara', 'route' => ['certificate.nirgam-utara'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Provisional Certificate', 'route' => ['certificate.provisional'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Testimonial Certificate', 'route' => ['certificate.testimonial'], 'icon' => 'fa-caret-right'],
                    ['title' => 'MOI Certificate', 'route' => ['certificate.moi'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Transcript Certificate', 'route' => ['certificate.transcript'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Issue History', 'route' => ['certificate.issue-history'], 'icon' => 'fa-history'],
                    ['title' => 'Custom Print', 'route' => ['certificate.generate'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Certificate Template', 'route' => ['certificate.template'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
           
            [
                'title' => 'Web Portal',
                'icon' => 'fa-globe',
                'color' => '#10b981',
                'items' => $generalSetting->web_cms ? [
                    [
                        'title' => 'Web CMS Dashboard',
                        'desc' => 'Manage web content',
                        'route' => ['web.admin.dashboard'],
                        'icon' => 'fa-globe',
                    ],
                ] : [],
            ],
            [
                'title' => 'Front Office',
                'icon' => 'fa-comment',
                'color' => '#f59e0b',
                'items' => $generalSetting->front_desk ? [
                    [
                        'title' => 'Postal Exchange',
                        'desc' => 'Manage postal records',
                        'route' => ['front.postal-exchange'],
                        'icon' => 'fa-caret-right',
                    ],
                    [
                        'title' => 'Visitor Log',
                        'desc' => 'Track visitors',
                        'route' => ['front.visitor'],
                        'icon' => 'fa-caret-right',
                    ],
                ] : [],
            ],            
            [
                'title' => 'Inventory',
                'icon' => 'fa-shopping-cart',
                'color' => '#ef4444',
                'items' => $generalSetting->inventory ? [
                    [
                        'title' => 'Class Assets',
                        'desc' => 'Manage class assets',
                        'icon' => 'fa-store',
                        'children' => [
                            ['title' => 'Assets', 'route' => ['inventory.assets'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Faculty/Sem Assets', 'route' => ['inventory.sem-assets'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Product/Assets',
                        'desc' => 'Manage products',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Product', 'route' => ['inventory.product.registration'], 'icon' => 'fa-plus'],
                            ['title' => 'Product Detail', 'route' => ['inventory.product'], 'icon' => 'fa-list'],
                            ['title' => 'Category', 'route' => ['inventory.product.category'], 'icon' => 'fa-list-alt'],
                        ],
                    ],
                    [
                        'title' => 'Customer',
                        'desc' => 'Manage customers',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Customer Detail', 'route' => ['inventory.customer'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['inventory.customer.registration'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Document Upload', 'route' => ['inventory.customer.document'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Create Notes', 'route' => ['inventory.customer.note'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Vendor',
                        'desc' => 'Manage vendors',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Vendor Detail', 'route' => ['inventory.vendor'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['inventory.vendor.registration'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Document Upload', 'route' => ['inventory.vendor.document'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Create Notes', 'route' => ['inventory.vendor.note'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Purchase',
                        'desc' => 'Manage purchases',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Purchase Details', 'route' => ['inventory.purchase'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Purchase Now', 'route' => ['inventory.purchase.registration'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Purchase Return', 'route' => ['inventory.vendor.document'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Sales',
                        'desc' => 'Manage sales',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Sales Details', 'route' => ['inventory.vendor'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Sales Now', 'route' => ['inventory.vendor.registration'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Sales Return', 'route' => ['inventory.vendor.document'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],
            [
                'title' => 'Library',
                'icon' => 'fa-book',
                'color' => '#ef4444',
                'items' => $generalSetting->library ? [
                    [
                        'title' => 'Books',
                        'desc' => 'Manage library books',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Book Detail', 'route' => ['library.book'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Add New', 'route' => ['library.book.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Bulk Import', 'route' => ['library.book.import'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Category', 'route' => ['library.book.category'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Members',
                        'desc' => 'Manage library members',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Membership', 'route' => ['library.member'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Student Member', 'route' => ['library.student'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Member', 'route' => ['library.staff'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Book Request',
                        'desc' => 'Manage book requests',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Student Request', 'route' => ['library.student-request'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Staff Request', 'route' => ['library.staff-request'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    ['title' => 'Issue History', 'route' => ['library.issue-history'], 'icon' => 'fa-history'],
                    ['title' => 'Return Period Over', 'route' => ['library.return-over'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Circulation Setting', 'route' => ['library.circulation'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            
            [
                'title' => 'Examination',
                'icon' => 'fa-line-chart',
                'color' => '#dc2626',
                'items' => $generalSetting->exam ? [
                    [
                        'title' => 'Online - MCQ Exam',
                        'desc' => 'Manage online exams',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            [
                                'title' => 'Question Bank',
                                'icon' => 'fa-caret-right',
                                'children' => [
                                    ['title' => 'Question', 'route' => ['mcq.question.index'], 'icon' => 'fa-caret-right'],
                                    ['title' => 'Group', 'route' => ['mcq.question.question-group'], 'icon' => 'fa-caret-right'],
                                    ['title' => 'Level', 'route' => ['mcq.question.question-level'], 'icon' => 'fa-caret-right'],
                                ],
                            ],
                            ['title' => 'Instruction', 'route' => ['mcq.exam.exam-instruction'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Online Exam', 'route' => ['mcq.exam.index'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Offline - Manual Exam',
                        'desc' => 'Manage manual exams',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Schedule Exam', 'route' => ['exam.schedule'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Mark Ledger', 'route' => ['exam.mark-ledger'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Exams Head', 'route' => ['exam'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Admit Card', 'route' => ['exam.admit-card'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Routine/Schedule', 'route' => ['exam.routine'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Grade/Mark/Ledger Sheet', 'route' => ['exam.mark-sheet'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],            
            [
                'title' => 'Hostel',
                'icon' => 'fa-bed',
                'color' => '#0891b2',
                'items' => $generalSetting->hostel ? [
                    [
                        'title' => 'Resident',
                        'desc' => 'Manage hostel residents',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Detail', 'route' => ['hostel.resident'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['hostel.resident.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Occupant History', 'route' => ['hostel.resident.history'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Hostel',
                        'desc' => 'Manage hostel details',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Hostel', 'route' => ['hostel'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Room Type', 'route' => ['hostel.room-type'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Food & Meal',
                        'desc' => 'Manage meal schedules',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Meal Schedule', 'route' => ['hostel.food'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Eating Time', 'route' => ['hostel.food.eating-time'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Food Category', 'route' => ['hostel.food.category'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Food Item', 'route' => ['hostel.food.item'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],
            [
                'title' => 'Transport',
                'icon' => 'fa-bus',
                'color' => '#06b6d4',
                'items' => $generalSetting->transport ? [
                    [
                        'title' => 'Traveller/User',
                        'desc' => 'Manage transport users',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Detail', 'route' => ['transport.user'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Registration', 'route' => ['transport.user.add'], 'icon' => 'fa-caret-right'],
                            ['title' => 'User History', 'route' => ['transport.user.history'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    ['title' => 'Route', 'route' => ['transport.route'], 'icon' => 'fa-caret-right'],
                    ['title' => 'Vehicle', 'route' => ['transport.vehicle'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Assignments',
                'icon' => 'fa-tasks',
                'color' => '#8b5cf6',
                'items' => $generalSetting->assignment ? [
                    ['title' => 'Assignment', 'route' => ['assignment'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Applications',
                'icon' => 'fa-file',
                'color' => '#14b8a6',
                'items' => $generalSetting->application ? [
                    ['title' => 'Application', 'route' => ['application.index'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Downloads',
                'icon' => 'fa-download',
                'color' => '#f43f5e',
                'items' => $generalSetting->upload_download ? [
                    ['title' => 'Upload & Download', 'route' => ['download'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Meetings',
                'icon' => 'fa-video-camera',
                'color' => '#0d9488',
                'items' => $generalSetting->meeting ? [
                    ['title' => 'Meeting - Remote Class', 'route' => ['meeting'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Info Center',
                'icon' => 'fa-bullhorn',
                'color' => '#f43f5e',
                'items' => $generalSetting->alert ? [
                    ['title' => 'User Notice', 'route' => ['info.notice'], 'icon' => 'fa-caret-right'],
                    ['title' => 'SMS / E-mail', 'route' => ['info.smsemail'], 'icon' => 'fa-caret-right'],
                ] : [],
            ],
            [
                'title' => 'Academic',
                'icon' => 'fa-graduation-cap',
                'color' => '#22c55e',
                'items' => $generalSetting->academic ? [
                    ['title' => 'Academic Dashboard', 'route' => ['academic.dashboard'], 'icon' => 'fa-tachometer'],
                    [
                        'title' => 'Academic Levels',
                        'desc' => 'Manage academic hierarchy',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Department Head', 'route' => ['department-head'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Department', 'route' => ['department'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Faculty', 'route' => ['faculty'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Semester', 'route' => ['semester'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Batch', 'route' => ['student-batch'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Routine', 'route' => ['routine.index'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Grading & Subjects',
                        'desc' => 'Manage subjects and grading',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Subject', 'route' => ['subject'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Grading', 'route' => ['grading'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Year, Month, Day',
                        'desc' => 'Manage academic calendar',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Year', 'route' => ['year'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Month', 'route' => ['month'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Day', 'route' => ['day'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Status Settings',
                        'desc' => 'Manage status settings',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Student Status', 'route' => ['student-status'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Attendance Status', 'route' => ['attendance-status'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Book Status', 'route' => ['book-status'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Hostel Bed Status', 'route' => ['bed-status'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Customer Status', 'route' => ['customer-status'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                    [
                        'title' => 'Dynamic Gallery',
                        'desc' => 'Manage dynamic content',
                        'icon' => 'fa-caret-right',
                        'children' => [
                            ['title' => 'Placement', 'route' => ['dynamic.placement'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Scholarship', 'route' => ['dynamic.scholarship'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Annexure', 'route' => ['dynamic.annexure'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Academic Info Level', 'route' => ['dynamic.academic-info-level'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Degree', 'route' => ['dynamic.degree'], 'icon' => 'fa-caret-right'],
                            ['title' => 'State', 'route' => ['dynamic.state'], 'icon' => 'fa-caret-right'],
                            ['title' => 'Application Type', 'route' => ['dynamic.application-type'], 'icon' => 'fa-caret-right'],
                        ],
                    ],
                ] : [],
            ],
            // [
            //     'title' => 'Help',
            //     'icon' => 'fa-question',
            //     'color' => '#6b7280',
            //     'items' => $generalSetting->help ? [
            //         ['title' => 'License Info', 'route' => ['license-info'], 'icon' => 'fa-caret-right'],
            //         ['title' => 'Test Demo', 'route' => [], 'url' => 'http://unlimitededufirm.com/demo-detail', 'external' => true, 'icon' => 'fa-caret-right'],
            //         ['title' => 'Video Tutorial', 'route' => [], 'url' => 'https://www.youtube.com/watch?v=2jgA9WY8IzQ&list=PLCtD_CGPAQJ2zSk5cDUkkfWGdtMGsF9n0', 'external' => true, 'icon' => 'fa-caret-right'],
            //         ['title' => 'Documentation', 'route' => [], 'url' => 'http://docs.unlimitededufirm.com', 'external' => true, 'icon' => 'fa-caret-right'],
            //         ['title' => 'Buy New License', 'route' => [], 'url' => 'https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988', 'external' => true, 'icon' => 'fa-caret-right'],
            //     ] : [],
            // ],
        ];

        // Build menu with resolved URLs and permissions
        $out = [];
        foreach ($menuGroups as $group) {
            $items = [];

            foreach ($group['items'] as $item) {
                // Resolve the best route name (your existing helper)
                $routeName = null;
                if (isset($item['route']) && !empty($item['route'])) {
                    $routeName = $this->firstExistingRoute($item['route']);
                }

                // Build and icon-map children first
                $children = [];
                if (!empty($item['children'] ?? [])) {
                    $children = $this->buildMenuChildren($item['children']);
                    $children = $this->applyIconMappingRecursive($children); // ⬅ meaningful icons for sub-levels
                }

                // Decide link vs branch
                if ($routeName) {
                    $items[] = [
                        'title'         => $item['title'],
                        'desc'          => $item['desc'] ?? '',
                        'icon'          => $this->iconForItem($item['title'] ?? '', $item['desc'] ?? '', $routeName, $item['icon'] ?? null),
                        'color'         => $group['color'] ?? '#4361ee',
                        'dark_color'    => $this->darkenColor($group['color'] ?? '#4361ee', -20),
                        'url'           => route($routeName),
                        'route'         => $routeName,
                        'data_title'    => Str::lower($item['title']),
                        'data_desc'     => Str::lower($item['desc'] ?? ''),
                        'data_route'    => Str::lower($routeName),
                        'route_display' => Str::title(str_replace('.', ' / ', $routeName)),
                        'children'      => $children,
                    ];
                } elseif (isset($item['url']) && !empty($item['external'])) {
                    $items[] = [
                        'title'         => $item['title'],
                        'desc'          => $item['desc'] ?? '',
                        'icon'          => $this->iconForItem($item['title'] ?? '', $item['desc'] ?? '', '', $item['icon'] ?? null),
                        'color'         => $group['color'] ?? '#4361ee',
                        'dark_color'    => $this->darkenColor($group['color'] ?? '#4361ee', -20),
                        'url'           => $item['url'],
                        'route'         => '',
                        'data_title'    => Str::lower($item['title']),
                        'data_desc'     => Str::lower($item['desc'] ?? ''),
                        'data_route'    => '',
                        'route_display' => $item['title'],
                        'children'      => [],
                        'external'      => true,
                    ];
                } elseif (!empty($children)) {
                    $items[] = [
                        'title'         => $item['title'],
                        'desc'          => $item['desc'] ?? '',
                        'icon'          => $this->iconForItem($item['title'] ?? '', $item['desc'] ?? '', '', $item['icon'] ?? null),
                        'color'         => $group['color'] ?? '#4361ee',
                        'dark_color'    => $this->darkenColor($group['color'] ?? '#4361ee', -20),
                        'url'           => '#',
                        'route'         => '',
                        'data_title'    => Str::lower($item['title']),
                        'data_desc'     => Str::lower($item['desc'] ?? ''),
                        'data_route'    => '',
                        'route_display' => $item['title'],
                        'children'      => $children,
                    ];
                }
            }

            if (!empty($items)) {
                $out[] = [
                    'title'      => $group['title'],
                    'group_slug' => Str::slug($group['title']),
                    'icon'       => $this->iconForGroup($group['title'], $group['icon'] ?? null), // ⬅ smarter group icons
                    'color'      => $group['color'],
                    'items'      => $items,
                ];
            }
        }

        return $out;
    }

    /* ================================
    |  Helpers: icon selection logic  |
    ================================ */

    private function isPlaceholderIcon(?string $icon): bool
    {
        $icon = $icon ? strtolower($icon) : '';
        return !$icon || in_array($icon, [
            'fa-caret-right','fa-angle-right','fa-long-arrow-right','fa-arrow-right',
            'fa-link','fa-circle','fa-dot-circle','fa-minus','fa-caret-down',
        ], true);
    }

    /** Map group title → meaningful icon (first match wins). */
    private function iconForGroup(string $title, ?string $current = null): string
    {
        $key = Str::slug($title);
        $map = [
            'dashboard'       => 'fa-gauge',
            'web-portal'      => 'fa-globe',
            'front-office'    => 'fa-building',
            'staff-student'   => 'fa-users',
            'accounts'        => 'fa-file-invoice-dollar',
            'inventory'       => 'fa-boxes-stacked',
            'library'         => 'fa-book-open',
            'attendance'      => 'fa-calendar-check',
            'examination'     => 'fa-square-poll-vertical',
            'certificate'     => 'fa-award',
            'hostel'          => 'fa-bed',
            'transport'       => 'fa-bus',
            'assignments'     => 'fa-clipboard-check',
            'applications'    => 'fa-file-pen',
            'downloads'       => 'fa-cloud-arrow-down',
            'meetings'        => 'fa-video',
            'info-center'     => 'fa-bullhorn',
            'academic'        => 'fa-graduation-cap',
            'help'            => 'fa-circle-question',
        ];

        if (isset($map[$key])) return $map[$key];

        // Keyword fallback
        $fallback = $this->iconByKeywords($title, '');
        if ($fallback) return $fallback;

        // Keep non-placeholder current icon
        return $this->isPlaceholderIcon($current) ? 'fa-layer-group' : $current;
    }

    /** Item-level mapping (title/desc/route). Keeps your good custom icons. */
    private function iconForItem(string $title, string $desc = '', string $route = '', ?string $current = null): string
    {
        if (!$this->isPlaceholderIcon($current)) {
            return $current; // respect explicit meaningful icon
        }
        $icon = $this->iconByKeywords($title.' '.$desc, $route);
        return $icon ?: 'fa-rectangle-list';
    }

    /** Recursively upgrade icons on children/sub-children. */
    private function applyIconMappingRecursive(array $children): array
    {
        foreach ($children as &$child) {
            $title = $child['title'] ?? ($child['data_title'] ?? 'Item');
            $desc  = $child['desc']  ?? ($child['data_desc']  ?? '');
            $route = $child['route'] ?? ($child['data_route'] ?? '');

            if ($this->isPlaceholderIcon($child['icon'] ?? null)) {
                $child['icon'] = $this->iconForItem($title, $desc, $route, $child['icon'] ?? null);
            }

            if (!empty($child['children'] ?? [])) {
                $child['children'] = $this->applyIconMappingRecursive($child['children']);
            }
        }
        unset($child);
        return $children;
    }

    /** Ordered keyword rules → FA icons (first match wins). */
    private function iconByKeywords(string $text, string $route = ''): ?string
    {
        $hay = Str::lower(trim($text.' '.$route));

        $rules = [
            // High-level
            [['dashboard','overview','home','main'],                 'fa-gauge'],
            [['web','cms','portal','site','page'],                   'fa-globe'],
            [['front office','desk','reception','postal','post'],    'fa-envelope-open-text'],
            [['visitor','gate','log'],                               'fa-id-badge'],

            // People & roles
            [['student','pupil'],                                    'fa-user-graduate'],
            [['guardian','parent'],                                  'fa-user-shield'],
            [['staff','employee','teacher','hr'],                    'fa-user-tie'],
            [['registration','enroll','admission','sign up'],        'fa-user-plus'],
            [['designation','role','position','title'],              'fa-briefcase'],

            // Files & docs
            [['document','doc','upload'],                            'fa-file-arrow-up'],
            [['import','bulk'],                                      'fa-file-import'],
            [['export'],                                             'fa-file-export'],
            [['note','remarks','memo'],                              'fa-note-sticky'],
            [['report','ledger','statement','history'],              'fa-file-lines'],
            [['print','admit card','id card'],                       'fa-id-card-clip'],
            [['template'],                                           'fa-clone'],

            // Accounts / finance
            [['fees','fee'],                                         'fa-receipt'],
            [['collection','receive'],                               'fa-hand-holding-dollar'],
            [['balance','dues'],                                     'fa-scale-balanced'],
            [['payment','online pay','gateway','card'],              'fa-credit-card'],
            [['cash book'],                                          'fa-book'],
            [['transaction','transfer'],                             'fa-right-left'],
            [['ledger'],                                             'fa-book-bookmark'],
            [['bank','cheque','account'],                            'fa-building-columns'],
            [['payroll','salary','wage'],                            'fa-sack-dollar'],
            [['account group','chart of account','coa','chart'],     'fa-diagram-project'],
            [['head'],                                               'fa-list-check'],

            // Inventory & sales
            [['inventory','stock','store'],                          'fa-boxes-stacked'],
            [['product','item','sku'],                               'fa-box-open'],
            [['category','tag'],                                     'fa-tags'],
            [['customer','client'],                                  'fa-user-tag'],
            [['vendor','supplier'],                                  'fa-store'],
            [['purchase','buy'],                                     'fa-cart-shopping'],
            [['sale','invoice','sell'],                              'fa-basket-shopping'],

            // Library
            [['library','book','books'],                             'fa-book-open'],
            [['member','membership','card'],                         'fa-id-card'],
            [['circulation','setting','config'],                     'fa-gear'],

            // Attendance
            [['attendance','present','absent'],                      'fa-calendar-check'],
            [['scan','qr'],                                          'fa-qrcode'],
            [['device','machine','biometric'],                       'fa-microchip'],
            [['monthly','calendar','routine'],                       'fa-calendar-days'],

            // Exams
            [['exam','mcq','online test'],                           'fa-laptop-code'],
            [['question','bank','group','level'],                    'fa-circle-question'],
            [['instruction'],                                        'fa-clipboard-list'],
            [['offline','manual','mark'],                            'fa-pen-to-square'],
            [['grade'],                                              'fa-ranking-star'],

            // Certificates
            [['certificate','bonafide','provisional','transcript'],  'fa-award'],

            // Hostel
            [['hostel','room','bed'],                                'fa-bed'],
            [['resident','occupant'],                                'fa-house-user'],
            [['meal','food','menu'],                                 'fa-utensils'],
            [['time'],                                               'fa-clock'],

            // Transport
            [['transport','route'],                                  'fa-route'],
            [['vehicle','bus','van'],                                'fa-bus'],

            // Misc features
            [['assignment','task'],                                  'fa-clipboard-check'],
            [['application','apply','form'],                         'fa-file-pen'],
            [['download','upload'],                                  'fa-cloud-arrow-down'],
            [['meeting','remote','class','zoom'],                    'fa-video'],
            [['notice','sms','email','alert','announcement'],        'fa-bullhorn'],

            // Academic
            [['academic','subject','semester','faculty','department','grading','year','month','day'], 'fa-graduation-cap'],

            // Help
            [['help','license','doc','video','tutorial','buy'],      'fa-circle-question'],
        ];

        foreach ($rules as [$keywords, $icon]) {
            foreach ($keywords as $kw) {
                if (Str::contains($hay, $kw)) {
                    return $icon;
                }
            }
        }
        return null;
    }

    protected function buildMenuChildren(array $children): array
    {
        $out = [];
        foreach ($children as $child) {
            if (isset($child['route']) && !empty($child['route'])) {
                $routeName = $this->firstExistingRoute($child['route']);
                if ($routeName) {
                    $out[] = [
                        'title' => $child['title'],
                        'desc' => $child['desc'] ?? '',
                        'icon' => $child['icon'] ?? 'fa-caret-right',
                        'url' => route($routeName),
                        'route' => $routeName,
                        'data_title' => Str::lower($child['title']),
                        'data_desc' => Str::lower($child['desc'] ?? ''),
                        'data_route' => Str::lower($routeName),
                        'route_display' => Str::title(str_replace('.', ' / ', $routeName)),
                        'children' => $this->buildMenuChildren($child['children'] ?? []),
                    ];
                }
            } elseif (isset($child['url']) && $child['external']) {
                $out[] = [
                    'title' => $child['title'],
                    'desc' => $child['desc'] ?? '',
                    'icon' => $child['icon'] ?? 'fa-caret-right',
                    'url' => $child['url'],
                    'route' => '',
                    'data_title' => Str::lower($child['title']),
                    'data_desc' => Str::lower($child['desc'] ?? ''),
                    'data_route' => '',
                    'route_display' => $child['title'],
                    'children' => [],
                    'external' => true,
                ];
            } elseif (isset($child['children']) && !empty($child['children'])) {
                $subChildren = $this->buildMenuChildren($child['children']);
                if (!empty($subChildren)) {
                    $out[] = [
                        'title' => $child['title'],
                        'desc' => $child['desc'] ?? '',
                        'icon' => $child['icon'] ?? 'fa-caret-right',
                        'url' => '#',
                        'route' => '',
                        'data_title' => Str::lower($child['title']),
                        'data_desc' => Str::lower($child['desc'] ?? ''),
                        'data_route' => '',
                        'route_display' => $child['title'],
                        'children' => $subChildren,
                    ];
                }
            }
        }
        return $out;
    }

    public function welcome()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login');
        }
    }

    public function accountDashboard(Request $request)
    {
        $data = [];

        // Active Year
        $year = Year::where('active_status', 1)->first();
        $activeYear = $year ? $year->title : date('Y');

        // Date filter
        $dateFilter = $this->getDateRangeFromRequest($request);
        $data['filter'] = [
            'start_date' => $dateFilter['start_date'],
            'end_date' => $dateFilter['end_date'],
            'quick_range' => $dateFilter['quick_range'],
            'activeYear' => $activeYear,
        ];

        // Fee collection statistics
        $data['feeCollectionIndicator'] = FeeCollection::whereBetween('date', [$dateFilter['start_date'], $dateFilter['end_date']])->sum('paid_amount') ?? 0;
        $data['salaryPayIndicator'] = SalaryPay::whereBetween('date', [$dateFilter['start_date'], $dateFilter['end_date']])->sum('paid_amount') ?? 0;
        $data['totalIncome'] = $data['feeCollectionIndicator'];
        $data['totalExpense'] = $data['salaryPayIndicator'];

        // Chart data (placeholders)
        $data['feeSalaryChart'] = null;
        $data['incomeVsExpenseChart'] = null;
        $data['feeCompare'] = null;
        $data['transactionChart'] = null;

        // Recent collections
        $data['recent_fees_collection'] = FeeCollection::whereBetween('date', [$dateFilter['start_date'], $dateFilter['end_date']])
            ->with('student', 'feeMaster')
            ->latest()
            ->limit(10)
            ->get();

        // Recent payroll payments
        $data['recent_payroll_pay'] = SalaryPay::whereBetween('date', [$dateFilter['start_date'], $dateFilter['end_date']])
            ->with('staffs')
            ->latest()
            ->limit(10)
            ->get();

        // Recent transactions
        $data['recent_transaction'] = Transaction::whereBetween('date', [$dateFilter['start_date'], $dateFilter['end_date']])
            ->with('trHead')
            ->latest()
            ->limit(10)
            ->get();

        return view(parent::loadDataToView($this->view_path . '.account'), compact('data'));
    }
}