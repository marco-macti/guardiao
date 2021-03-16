<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class EAuditor implements ToCollection, WithEvents
{
    public $sheetNames;
    public $sheetData;
	
    public function __construct(){
        $this->sheetNames = [];
	    $this->sheetData  = [];
    }

    
  /**
   * @param array $row
   * @example php artisan make:import TableImport --model=User cria automatizacao para importar para model User
   * @return \Illuminate\Database\Eloquent\Model|null
   */
  // public function model(array $row)
  public function collection(Collection $rows)
  {
    if(!empty($rows)){
        $this->sheetData[] = $rows;
    }
  }

  public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
            	$this->sheetNames[] = $event->getSheet()->getTitle();
            } 
        ];
    }
}
