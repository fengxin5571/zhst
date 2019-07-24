<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/6/17
 * Time: 2:52 PM
 */
namespace App\Admin\Extensions\Nav;

class Links
{
    public function __toString()
    {
        return <<<HTML

<li>
    <a href="/admin/settings">
      <i class="fa fa-gears"></i>
      <span>系统配置</span>
    </a>
</li>



HTML;
    }
}