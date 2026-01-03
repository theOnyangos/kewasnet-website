<?php

namespace App\Services;

use App\Models\JobApplication as JobApplicationModel;
use Carbon\Carbon;

class OpportunitiesService 
{
    // This method returns job opportunities Html
    public function getJobOpportunitiesHtml($jobOpportunities)
    {

        if (empty($jobOpportunities)) {
            return '<div class="flex justify-center items-center h-[300px]">
                        <div class="flex flex-col justify-center items-center gap-5">
                            <ion-icon name="warning-outline" class="text-gray-500" style="font-size: 50px;"></ion-icon>
                            <p class="text-gray-500 font-bold text-lg">No job opportunities posted!</p>
                        </div>
                    </div>';
        }

        $html = '';

        foreach ($jobOpportunities as $index => $jobOpportunity) {

            // Set uploaded data
            $uploadedDate = Carbon::parse($jobOpportunity['created_at'])->diffForHumans();

            // set application deadline date
            $applicationDeadline = Carbon::parse($jobOpportunity['application_deadline'])->format('d M, Y');

            // Get the last item
            $isLast = $index === count($jobOpportunities) - 1;

            $jobOpportunity['uploaded_date'] = $uploadedDate;
            $jobOpportunity['app_deadline'] = $applicationDeadline;

            $html .= '<div class="flex flex-col md:flex-row justify-between items-start md:items-center '.($isLast ? '' : 'border-b border-borderColor').' mb-5">
                          <div class="mb-5 flex justify-center items-center gap-5">
                              <img src="'. base_url('jobs.jpg') .'" alt="Jobs Logo" class="w-[100px] h-[100px] object-cover rounded">
                              <div class="">
                                <!-- Job Title -->
                                <h5 class="text-lg text-gray-800 font-bold leading-none capitalize">'. substr($jobOpportunity['title'], 0, 35) ."... " .'</h5>

                                <!-- Total applicants -->
                                <div class="flex gap-3 items-center">
                                  <p class="text-base text-gray-500 font-bold text-md">Total Applicants: <span class="text-sm">('. $this->getTotalApplicantsCount($jobOpportunity['id']) .')</span></p>
                                </div>

                                <!-- Job position and date posted -->
                                <div class="flex gap-3 items-center">
                                  <a href="#" class="text-gray-500 text-sm font-bold">'. $jobOpportunity['uploaded_date'] .'</a>
                                </div>
                              </div>
                          </div>

                          <div class="mb-5 flex flex-col justify-center items-center">
                              <div class="">
                                <p class="text-gray-800 font-bold text-lg">'.$jobOpportunity['employment_type']  .'</p>
                                <p class="text-base text-gray-500 font-bold text-md">'. $jobOpportunity['location'] .'</p>
                              </div>
                          </div>

                          <div class="mb-5 flex flex-col justify-center items-center">
                            <!-- Job Type -->
                              <div class="">
                                <p class="text-gray-800 font-bold text-lg">Deadline</p>
                                <p class="text-base text-blue-700 rounded-full font-bold text-md">'. $jobOpportunity['app_deadline'] .'</p>
                              </div>
                          </div>

                          <div class="mb-5 flex flex-col justify-center items-center">
                            <!-- Download Job Description -->
                              <div class="">
                              '.($jobOpportunity['file_path'] != null ? '<a href="'. $jobOpportunity['file_path'] .'" download="'. url_title($jobOpportunity['title']) .'" class="text-base text-gray-800 font-bold text-md hover:underline">Download Job Description Document</a>' : '<a href="javascript:;" onclick="viewJobOpportunityDetails(this, '.htmlspecialchars(json_encode($jobOpportunity)).')" class="text-base text-gray-800 font-bold text-md hover:underline">View Application Details</a>').'
                                
                              </div>
                          </div>

                          <div class="mb-5 flex flex-col justify-center items-center">
                            <button onclick="handleApplyJobOpportunity(this, '.htmlspecialchars(json_encode($jobOpportunity)).')" type="button" id="openJobApplicationModal" class="block text-center bg-gradient-to-r from-primary to-secondary text-white roboto py-2 px-5 rounded-full shadow hover:shadow-md">Apply Now</button>
                          </div>
                      </div>';
        }

        return $html;
    }

    // This method gets the total applicants count
    public function getTotalApplicantsCount($jobOpportunityId)
    {
        $jobApplicationModel = new JobApplicationModel();

        return $jobApplicationModel->where('opportunity_id', $jobOpportunityId)->countAllResults();
    }
}