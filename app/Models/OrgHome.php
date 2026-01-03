<?php

namespace App\Models;

use CodeIgniter\Model;

class OrgHome extends Model
{
    protected $table            = 'org_home';
    protected $primaryKey       = 'org_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['org_name', 'org_title', 'description', 'org_image_url', 'org_video_url', 'org_doc_url', 'org_published_state'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'org_created_at';
    protected $updatedField  = 'org_updated_at';
    protected $deletedField  = 'org_deleted_at';

    //this method gets all org home data
    public function orgHomeData()
    {
        return $this->where('org_published_state', 'published')->findAll();
    }

    //this method gets all draft org home data
    public function draftOrgHomeData()
    {
        return $this->where('org_published_state', 'draft')->findAll();
    }

    //this method gets org home data by id
    public function orgHomeDataById($orgId)
    {
        return $this->find($orgId);
    }

    //this method is used to get the org home data by id
    public function getOrgHomeDataById($orgId)
    {
        return $this->find($orgId);
    }
    //this method is used to store the org home data
    public function storeOrgData($data)
    {
        $this->insert($data);
        return $this->insertID;
    }

    //this method is used to update the org home data
    public function updateOrgHomeData($data)
    {
        $orgId = $data['org_id'];
        $this->update($orgId, $data);
        return $this->affectedRows();
    }

    //this method is used to delete the org home data
    public function deleteOrgHomeData($orgId)
    {
        return $this->delete($orgId);
    }
}
