<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\CourseLectures;

class LectureService
{
    // Render lecture section HTML
    public function renderLectureSections($sections)
    {
        $html = '';

        if (empty($sections)) {
            $html .= '<div class="col-md-12 card card-default card-md mt-4">
                <div class="d-flex justify-content-center align-items-center" style="height: 250px;">
                    <div class="text-center">
                        <img src="' . base_url() . 'backend/img/svg/2.png" alt="Admin Empty" class="img-fluid" />
                        <h6 class="mt-3">No lectures found</h6>
                    </div>
                </div>
            </div>';
        } else {
            $count = 0;

            foreach ($sections as $section) {
                $count++;
                // Get the lectures for the section
                $lectures = (new CourseLectures())->where('section_id', $section['id'])->orderBy('id', 'DESC')->findAll();

                $html .= view('backend/pages/e-learning/lecture_section', [
                    'section' => $section,
                    'lectures' => $lectures,
                    'count' => $count,
                ]);
            }

        }

        return $html;
    }
}