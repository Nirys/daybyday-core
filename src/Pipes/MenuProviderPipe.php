<?php
namespace SpiritSystems\DayByDay\Core\Pipes;

use Illuminate\Support\Facades\Auth;

class MenuProviderPipe {

    public function handle($menuItems, $next){

        $menuItems[] = [
            'type' => 'html',
            'content' => '<p class=" list-group-item siderbar-top" title=""><img src="' . url('images/daybyday-logo-white.png') .'" alt="" style="width: 100%; margin: 1em 0;"></p>'
        ];

        $menuItems[] = [
            'type' => 'top-item',
            'iconClass' => 'fa fa-home',
            'title' => __('Dashboard'),
            'href' => route('dashboard')
        ];

        $menuItems[] = [
            'type' => 'top-item',
            'iconClass' => 'fa fa-user',
            'title' => __('Profile'),
            'href' => route('users.show', Auth::user()->external_id)
        ];

        $clientItem =[
            'type' => 'top-item-container',
            'iconClass' => 'fa fa-user-secret',
            'title' => __('Clients'),
            'id' => 'clients',
            'children' => [
                [
                    'iconClass' => 'bullet-point',
                    'href' => route('clients.index'),
                    'title' => __('All Clients'),                
                ]
            ]
        ];

        if( Auth::user()->can('client-create')){
            $clientItem['children'][] = [
                'iconClass' => 'bullet-point',
                'href' => route('clients.create'),
                'title' => __('New  Client'),                
            ];
        }
        $menuItems[] = $clientItem;

        $menuItems[] = $this->createModuleItem('fa-briefcase','Project','Projects','projects', 'project');
        $menuItems[] = $this->createModuleItem('fa-tasks','Task','Tasks','tasks','task');
        $menuItems[] = $this->createModuleItem('fa-users','User','Users','users','user');
        $leads = $this->createModuleItem('fa-hourglass','Opportunity','Opportunities','leads','lead');
        $leads['children'][] = ['iconClass' => 'bullet-point','href' =>route('leads.unqualified'), 'title' => __('Unqualified Opportunities')];
        $menuItems[] = $leads;

        if(Auth::user()->can('calendar-view')){
            $menuItems[] = [
                'type' => 'top-item-container',
                'iconClass' => 'fa fa-calendar',
                'title' => __('Appointments'),
                'id' => 'appointments',
                'children' => [
                    [
                        'iconClass' => 'bullet-point',
                        'href' => route('appointments.calendar'),
                        'title' => __('Calendar'),                
                    ]
                ]
            ];
        }

        $hrItem = [
            'type' => 'top-item-container',
            'iconClass' => 'fa fa-handshake',
            'title' => __('HR'),
            'id' => 'hr',
            'children' => []
        ];
        if(Auth::user()->can('absence-view')){
            $hrItem['children'][] = [
                'iconClass' => 'bullet-point',
                'href' => route('absence.index'),
                'title' => __('Absence overview'),                
            ];
        }
        if(Auth::user()->can('absence-manage')){
            $hrItem['children'][] = [
                'iconClass' => 'bullet-point',
                'href' => route('absence.create'),
                'title' => __('Register absence'),                
            ];
        }
        $hrItem['children'][] = [
            'iconClass' => 'bullet-point',
            'href' => route('departments.index'),
            'title' => __('Departments')
        ];
        $menuItems[] = $hrItem;

        if(Auth::user()->hasRole('administrator') || Auth::user()->hasRole('owner')){
            $menuItems[] = [
                'type' => 'top-item-container',
                'iconClass' => 'fa fa-cog',
                'title' => __('Settings'),
                'id' => 'settings',
                'children' => [
                    ['iconClass' => 'bullet-point', 'href' => route('settings.index'), 'title' => __('Overall Settings')],
                    ['iconClass' => 'bullet-point', 'href' => route('roles.index'), 'title' => __('Role &amp; Permissions Management')],
                    ['iconClass' => 'bullet-point', 'href' => route('integrations.index'), 'title' => __('Integrations')],
                ]
            ];
        }
        
        return $next($menuItems);
    }

    protected function createModuleItem($faIcon, $singular, $plural, $modulePrefix, $permissionPrefix){
        $moduleItem =[
            'type' => 'top-item-container',
            'iconClass' => 'fa ' . $faIcon,
            'title' => $plural,
            'id' => strtolower($modulePrefix),
            'children' => [
                [
                    'iconClass' => 'bullet-point',
                    'href' => route($modulePrefix . '.index'),
                    'title' => __('All ' . $plural),                
                ]
            ]
        ];

        if( Auth::user()->can($permissionPrefix . '-create')){
            $moduleItem['children'][] = [
                'iconClass' => 'bullet-point',
                'href' => route($modulePrefix . '.create'),
                'title' => __('New  ' . $singular),                
            ];
        }
        
        return $moduleItem;
    }
}