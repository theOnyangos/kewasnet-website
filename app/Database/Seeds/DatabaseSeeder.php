<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
       // $this->call(CategorySeeder::class);
        // Run RoleSeeder first since UserSeeder depends on it
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(PillarCategorySeeder::class);
        $this->call(PillarSeeder::class);
        $this->call(PillarLinksSeeder::class);
        $this->call(SmsSettingsSeeder::class);
        $this->call(EmailSettingsSeeder::class);
        $this->call(FacebookSettingsSeeder::class);
        $this->call(GoogleSettingsSeeder::class);
        $this->call(MpesaSettingsSeeder::class);
        $this->call(PartnerSeeder::class);
        $this->call(SocialLinkSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(EventOrganizerSeeder::class);
        $this->call(EventRegistrationSeeder::class);
        $this->call(EventTicketsSeeder::class);
        $this->call(TaskIconSeeder::class);
        $this->call(BlogCategoriesSeeder::class);
        $this->call(BlogTagsSeeder::class);
        $this->call(BlogPostsSeeder::class);
        $this->call(BlogPostViewsSeeder::class);
        $this->call(UserBookmarkSeeder::class);
        $this->call(DocumentTypeSeeder::class);
        $this->call(ResourceCategorySeeder::class);
        $this->call(ResourceSeeder::class);
        $this->call(ContributorSeeder::class);
        $this->call(ResourceContributorSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(DiscussionSeeder::class);
        $this->call(ForumSeeder::class);
        $this->call(JobOpportunitiesSeeder::class);
        $this->call(JobApplicationsSeeder::class);
        $this->call(SitemapSettingsSeeder::class);
        $this->call(DocumentResourceCategoriesSeeder::class);
    }
}
