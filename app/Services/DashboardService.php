<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Repositories\BaseRepository;

class DashboardService extends BaseService
{
    /**
     * Dashboard doesn't need a specific repository as it aggregates data from multiple sources.
     * We return an empty repository if needed, but we won't use it.
     */
    public function __construct()
    {
        // Skip calling parent constructor to avoid needing a repository
    }

    protected function getRepository(): BaseRepository
    {
        // This is required by abstract, but we won't use it in this specific service
        throw new \Exception("DashboardService doesn't use a single repository.");
    }
}
