<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\TeamRepository;

class TeamService extends BaseService
{
    protected TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->repository = $teamRepository;
    }

    protected function getRepository(): BaseRepository
    {
        return $this->teamRepository;
    }
}
