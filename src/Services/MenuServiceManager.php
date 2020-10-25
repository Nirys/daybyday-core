<?php
namespace SpiritSystems\DayByDay\Core\Services;

use Illuminate\Pipeline\Pipeline;

class MenuServiceManager {
    protected $_providers = [];

    public function addProvider($className){
        $this->_providers[] = $className;
    }

    public function items(){
        $menu = [];

        return app(Pipeline::class)
            ->send($menu)
            ->through($this->_providers)
            ->then(function($menu){
                return $menu;
            });
    }

    public function render(){
        $items = $this->items();
        $output = [];
        foreach($items as $item){
            $output[] = $this->renderItem($item);
        }

        return implode("\n", $output);
    }

    protected function renderItem($item){
        switch($item['type']){
            case 'top-item':
                return '<a href="' . $item['href'] . '" class=" list-group-item"><i class="' . $item['iconClass'] . ' sidebar-icon"></i>'
                . '<span>' . $item['title'] . '</span></a>';
                break;
            case 'top-item-container':
                $data = '<a href="#' . $item['id'] . '" class="list-group-item" data-toggle="collapse">';
                $data .= '<i class="' . $item['iconClass'] . ' sidebar-icon"></i><span>' . $item['title'] . '</span>';
                $data .= '<i class="icon ion-md-arrow-dropup arrow-side sidebar-arrow"></i></a>';
                $data .= '<div class="collapse" id="' . $item['id'] . '">';
                foreach($item['children'] as $child){
                    $data .= '<a href="' . $child['href'] . '" class="list-group-item childlist">';
                    $data .= '<i class="' . $child['iconClass'] . '"><span></span></i>' . $child['title'] . '</a>';
                }
                $data .= '</div>';
                return $data;
                break;
            default:
                return $item['content'];
        }
    }
}