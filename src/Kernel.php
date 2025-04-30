<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getLogDir(): string
    {
        // Use the system temp directory instead
        return sys_get_temp_dir().'/CitiCore/log/'.$this->environment;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/CitiCore/cache/'.$this->environment;
    }
}
