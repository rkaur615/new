<?php


namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Routing\Controller as BaseController;

class MenuController extends BaseController
{
    /*
    Requirements:
    - the eloquent expressions should result in EXACTLY one SQL query no matter the nesting level or the amount of menu items.
    - it should work for infinite level of depth (children of childrens children of childrens children, ...)
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - open the `app/Http/Controllers/MenuController` file
    - eager loading cannot load deeply nested relationships
    - a recursive function in php is needed to structure the query results
    - partial or not working answers also get graded so make sure you commit what you have


    Sample response on GET /menu:
    ```json
    [
        {
            "id": 1,
            "name": "All events",
            "url": "/events",
            "parent_id": null,
            "created_at": "2021-04-27T15:35:15.000000Z",
            "updated_at": "2021-04-27T15:35:15.000000Z",
            "children": [
                {
                    "id": 2,
                    "name": "Laracon",
                    "url": "/events/laracon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 3,
                            "name": "Illuminate your knowledge of the laravel code base",
                            "url": "/events/laracon/workshops/illuminate",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 4,
                            "name": "The new Eloquent - load more with less",
                            "url": "/events/laracon/workshops/eloquent",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "Reactcon",
                    "url": "/events/reactcon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 6,
                            "name": "#NoClass pure functional programming",
                            "url": "/events/reactcon/workshops/noclass",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 7,
                            "name": "Navigating the function jungle",
                            "url": "/events/reactcon/workshops/jungle",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                }
            ]
        }
    ]
     */

    public function getMenuItems() {
        $menuAll = MenuItem::all();
        
        $menuArr = $parentArr = $finalArr = $this->unique_menu = array();

        foreach($menuAll as $m){
            $menuArr[$m->id] = $m;
            $parentArr[$m->parent_id][$m->id] = $m;
        }
        
        foreach($menuArr as $key=> $m){
            if(!isset($this->unique_menu[$key])){
                $child_arr = array('id' => $m->id,'name' => $m->name,'url' => $m->url,'parent_id'=>$m->parent_id,
                                'created_at' => $m->created_at,'updated_at' => $m->updated_at);
                $child_arr['children'] = $this->get_child_menus($key,$parentArr);
                $finalArr[] = $child_arr;
                $this->unique_menu[$key] = $key;
            }
        }
        
        return json_encode($finalArr);
        
        // throw new \Exception('implement in coding task 3');
    }
    /***
     * 
     * 
     * GET DETAIL OF CHILD MENU HIRARCHY
     * 
     */
    public function get_child_menus($key,$parentArr){
        $menu_list = [];
        if(isset($parentArr[$key])){
            foreach($parentArr[$key] as $k=>$p1){
                $this->unique_menu[$p1->id] = $p1->id;
                $child_arr = array('id' => $p1->id,'name' => $p1->name,'url' => $p1->url,'parent_id'=>$p1->parent_id,
                'created_at' => $p1->created_at,'updated_at' => $p1->updated_at);
                $child_arr['children'] = $this->get_child_menus($p1->id,$parentArr);
                $menu_list[] = $child_arr;
                
            }
        }
        return $menu_list;
    }
}
