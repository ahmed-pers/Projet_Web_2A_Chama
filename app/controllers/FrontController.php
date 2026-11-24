<?php

class FrontController extends Controller
{
    private Service $services;
    private Counter $counters;
    private ServiceCounter $links;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->services = new Service($connection);
        $this->counters = new Counter($connection);
        $this->links = new ServiceCounter($connection);
    }

    public function dashboard(): void
    {
        $this->render('front/dashboard', [
            'services' => $this->services->all(),
            'counters' => $this->counters->all(),
        ], 'Front Office - Smart Queue');
    }

    public function services(): void
    {
        $services = [];

        foreach ($this->services->all() as $service) {
            $service['counters'] = $this->links->countersFor((int) $service['id']);
            $services[] = $service;
        }

        $this->render('front/services', ['services' => $services], 'Our services');
    }
}
