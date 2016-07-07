<?php

namespace App\Entities\Projects;

use Laracasts\Presenter\Presenter;

class ProjectPresenter extends Presenter
{
    public function customerNameAndEmail()
    {
        return $this->customer_id ? $this->customer->name  . ' (' . $this->customer->email . ')' : '-';
    }

    public function projectLink()
    {
        return link_to_route('projects.show', $this->name, [$this->id]);
    }

    public function status()
    {
        return getProjectStatusesList($this->entity->status_id);
    }

}