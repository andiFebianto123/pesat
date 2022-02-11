<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\Revision;

class CustomRevision extends Revision
{
    public function userResponsible()
    {
        if (empty($this->user_id) || empty($this->model_user)) { return false; }
        if (!class_exists($this->model_user)) {
            return false;
        }
        return $this->model_user::find($this->user_id);
    }
}
