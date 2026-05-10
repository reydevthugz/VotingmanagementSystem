<?php
namespace App\Controllers;

use App\Models\DashboardStats;
use Throwable;

class HomeController extends BaseController
{
    public function redirectToDashboard(): void
    {
        $role = $_SESSION['user']['role'] ?? '';
        if ($role === 'student') {
            $this->redirect('student/dashboard');
        }

        $this->redirect('dashboard');
    }

    public function index(): void
    {
        $this->render('admin/dashboard', ['pageTitle' => 'Dashboard']);
    }

    public function studentDashboard(): void
    {
        $this->render('student/dashboard', ['pageTitle' => 'Student Dashboard']);
    }

    public function dashboardStats(): void
    {
        try {
            $stats = (new DashboardStats())->summary();
            $this->json([
                'ok' => true,
                'data' => $stats,
            ]);
        } catch (Throwable $exception) {
            $this->json([
                'ok' => false,
                'message' => 'Failed to load dashboard statistics.',
            ], 500);
        }
    }

    public function votesTrend(): void
    {
        try {
            $days = (int) ($_GET['days'] ?? 7);
            $trend = (new DashboardStats())->votesTrend($days);
            $this->json([
                'ok' => true,
                'data' => $trend,
            ]);
        } catch (Throwable $exception) {
            $this->json([
                'ok' => false,
                'message' => 'Failed to load chart data.',
            ], 500);
        }
    }
}
