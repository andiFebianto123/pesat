<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Venturecraft\Revisionable\Revisionable;
use Venturecraft\Revisionable\RevisionableTrait;

trait CustomRevisionableTrait
{
    use RevisionableTrait;


    public function postForceDelete()
    {
        if (empty($this->revisionForceDeleteEnabled)) {
            return false;
        }

        if ((!isset($this->revisionEnabled) || $this->revisionEnabled)
            && (($this->isSoftDelete() && $this->isForceDeleting()) || !$this->isSoftDelete())) {

            $revisions[] = array(
                'revisionable_type' => $this->getMorphClass(),
                'revisionable_id' => $this->getKey(),
                'key' => self::CREATED_AT,
                'old_value' => $this->{self::CREATED_AT},
                'new_value' => null,
                'user_id' => $this->getSystemUserId(),
                'model_user' => $this->getSystemUserModel(),
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );

            $revision = Revisionable::newModel();
            \DB::table($revision->getTable())->insert($revisions);
            \Event::dispatch('revisionable.deleted', array('model' => $this, 'revisions' => $revisions));
        }
    }

    public function getAdditionalFields()
    {
        $additional = [];
        //Determine if there are any additional fields we'd like to add to our model contained in the config file, and
        //get them into an array.
        $fields = config('revisionable.additional_fields', []);
        foreach ($fields as $field) {
            if (Arr::has($this->originalData, $field)) {
                $additional[$field] = Arr::get($this->originalData, $field);
            }
        }
        $additional['model_user'] = $this->getSystemUserModel();
        return $additional;
    }

    public function getSystemUserId()
    {
        try {
            if (\Auth::check()) {
                return \Auth::user()->getAuthIdentifier();
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function getSystemUserModel()
    {
        try {
            if (\Auth::check()) {
                $model = get_class(\Auth::user());
                return $model;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
