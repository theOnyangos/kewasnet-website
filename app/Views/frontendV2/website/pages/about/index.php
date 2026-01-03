<?php 
    use App\Helpers\UrlHelper;
    $currentUrl = new UrlHelper();
    
    session()->set([ 'redirect_url' => $currentUrl::currentUrl() ]);
?>

<?= $this->extend('frontendV2/website/layouts/main') ?>

<!--  Section Title Block  -->
<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<!--  Section Content Block  -->
<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-secondary text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">About KEWASNET</h1>
            <p class="text-xl max-w-3xl mx-auto">
                Kenya's premier knowledge-sharing network driving excellence in Water, Sanitation, and Hygiene
            </p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Our Mission</h2>
                    <p class="text-lg text-dark mb-8">
                        To facilitate knowledge sharing, capacity building, and collaborative action among WASH sector stakeholders in Kenya, driving sustainable solutions for improved water, sanitation, and hygiene services for all.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Facilitate knowledge exchange and learning</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Build capacity across the WASH sector</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-dark">Promote sustainable WASH solutions</p>
                        </div>
                    </div>
                </div>
                <div class="bg-secondaryShades-100 rounded-lg p-8">
                    <h3 class="text-3xl font-bold mb-6 text-primaryShades-800">Our Vision</h3>
                    <p class="text-lg text-dark">
                        A Kenya where all people have sustainable access to safe water, adequate sanitation, and practice good hygiene, supported by an informed and collaborative WASH sector.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Our Core Values</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    The principles that guide our work and define our commitment to the WASH sector
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Collaboration</h3>
                    <p class="text-dark">
                        We believe in the power of partnership and collective action to achieve sustainable WASH outcomes.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-secondary rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Excellence</h3>
                    <p class="text-dark">
                        We strive for the highest standards in all our work, continuously improving our services and impact.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-dark rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-primaryShades-800">Innovation</h3>
                    <p class="text-dark">
                        We embrace new ideas, technologies, and approaches to address evolving WASH challenges.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-4xl font-bold mb-8 text-center text-primaryShades-800">Our Story</h2>
                <div class="space-y-6 text-lg text-dark">
                    <p>
                        KEWASNET was established in response to the growing need for coordinated knowledge sharing and collaboration in Kenya's Water, Sanitation, and Hygiene (WASH) sector. Recognizing that sustainable solutions require collective wisdom and shared learning, our founders envisioned a platform that would bring together diverse stakeholders to drive sector-wide improvements.
                    </p>
                    <p>
                        Since our inception, we have grown from a small network of passionate professionals to Kenya's leading knowledge-sharing platform for the WASH sector. Our journey has been marked by significant milestones, including the development of our comprehensive Knowledge Sharing Platform, the facilitation of numerous sector dialogues, and the production of influential policy briefs that have shaped national WASH strategies.
                    </p>
                    <p>
                        Today, KEWASNET serves as a critical bridge between research and practice, policy and implementation, bringing together government agencies, development partners, civil society organizations, private sector players, and academic institutions. Our work continues to evolve, always guided by our commitment to creating a Kenya where everyone has access to safe water, adequate sanitation, and practices good hygiene.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-6 text-primaryShades-800">Leadership Team</h2>
                <p class="text-xl text-dark max-w-3xl mx-auto">
                    Meet the dedicated professionals driving KEWASNET's mission forward
                </p>
            </div>
            <!-- Team Grid Container -->
            <div id="teamGrid" class="grid md:grid-cols-3 gap-8">
                <!-- Team members will be dynamically loaded here -->
            </div>
        </div>
    </section>
<?= $this->endSection() ?>

<!--  Section Scripts Block  -->
<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Team data JSON object
    const teamData = {
        "team": [
            {
                "id": 1,
                "name": "Malesi Shivaji",
                "position": "Chief Executive Officer",
                "description": "Leading KEWASNET's strategic vision with over 15 years of experience in WASH sector development and policy implementation.",
                "image": "<?= base_url('members/Malesi-Shivaji.jpg') ?>",
                "linkedin": "https://www.linkedin.com/in/malesi-shivaji-1978-04-04-sam/",
                "experience": "15+ years"
            },
            {
                "id": 2,
                "name": "Mercy Achando",
                "position": "Financial And Admin Officer",
                "description": "Overseeing program implementation and stakeholder engagement across Kenya's 47 counties with expertise in project management.",
                "image": "<?= base_url('members/Mercy-Achando.jpg') ?>",
                "linkedin": "https://www.linkedin.com/in/mercy-a-4a9492aa/?  utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app",
                "experience": "12+ years"
            },
            {
                "id": 3,
                "name": "Joan Kones",
                "position": "National Research & Project Lead",
                "description": "Managing our knowledge resources and ensuring effective information sharing across the network with focus on digital innovation.",
                "image": "<?= base_url('members/Joan-Kones.jpg') ?>",
                "linkedin": "https://www.linkedin.com/in/joan-kones-mph-127b2b86/",
                "experience": "8+ years"
            },
            {
                "id": 4,
                "name": "Lucy Mosoito",
                "position": "Projects Officer",
                "description": "Leading technical standards and quality assurance for WASH interventions with extensive field experience in rural water systems.",
                "image": "<?= base_url('members/Lucy-Mosoito.jpg') ?>",
                "linkedin": "https://www.linkedin.com/in/lucy-mosoito-a56211150/",
                "experience": "18+ years"
            },
            {
                "id": 5,
                "name": "Julia Ayieko",
                "position": "Communications Officer",
                "description": "Building and maintaining strategic partnerships with government, donors, and civil society for sustainable WASH outcomes.",
                "image": "<?= base_url('members/Julie-Ayieko.jpg') ?>",
                "linkedin": "https://www.linkedin.com/in/julia-ayieko-mprsk/",
                "experience": "10+ years"
            },
            {
                "id": 6,
                "name": "Daniel Siata",
                "position": "Finance",
                "description": "Driving research initiatives and innovative solutions for complex WASH challenges through evidence-based approaches.",
                "image": "<?= base_url('members/daniel-siata.jpeg') ?>",
                "linkedin": "https://www.linkedin.com/in/daniel-siata-a19aa4179/",
                "experience": "9+ years"
            }
        ]
    };

    // Function to render team members
    function renderTeamMembers() {
        const teamGrid = $('#teamGrid');
        teamGrid.empty(); // Clear existing content
        
        // Add loading animation
        teamGrid.html('<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div><p class="mt-4 text-dark">Loading team members...</p></div>');
        
        // Simulate loading delay for better UX
        setTimeout(() => {
            teamGrid.empty();
            
            // Render each team member
            $.each(teamData.team, function(index, member) {
                const teamMemberCard = `
                    <div class="team-member bg-white rounded-lg p-8 text-center shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl" data-aos="fade-up" data-aos-delay="${index * 100}">
                        <div class="relative mb-6">
                            <div class="w-32 h-32 mx-auto mb-4 relative overflow-hidden rounded-full border-4 border-primary/20">
                                <img src="${member.image}" alt="${member.name}" class="w-full h-full object-cover" 
                                     onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-lightGray flex items-center justify-center\\'>
                                         <svg class=\\'w-16 h-16 text-primary/50\\' fill=\\'currentColor\\' viewBox=\\'0 0 20 20\\'>
                                             <path fill-rule=\\'evenodd\\' d=\\'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z\\' clip-rule=\\'evenodd\\' />
                                         </svg>
                                     </div>'">
                            </div>
                            <div class="absolute top-2 right-2">
                                <span class="inline-block bg-primary text-white text-xs px-2 py-1 rounded-full">
                                    ${member.experience}
                                </span>
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold mb-2 text-primaryShades-800">${member.name}</h3>
                        <p class="text-secondary font-semibold mb-4">${member.position}</p>
                        <p class="text-dark text-sm mb-6 leading-relaxed">${member.description}</p>
                        
                        <!-- Social Links -->
                        <div class="flex justify-center space-x-4">
                            <a href="${member.linkedin}" target="_blank" rel="noopener noreferrer" 
                               class="linkedin-link inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full hover:bg-blue-700 transition-colors duration-300"
                               title="Connect on LinkedIn">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                `;
                
                teamGrid.append(teamMemberCard);
            });
            
            // Add click tracking for LinkedIn links
            $('.linkedin-link').on('click', function(e) {
                const memberName = $(this).closest('.team-member').find('h3').text();
                console.log(`LinkedIn clicked for: ${memberName}`);
                
                // Optional: Add analytics tracking
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'social_link_click', {
                        'social_platform': 'linkedin',
                        'team_member': memberName
                    });
                }
            });
            
            // Add hover effects
            $('.team-member').hover(
                function() {
                    $(this).addClass('shadow-2xl');
                },
                function() {
                    $(this).removeClass('shadow-2xl');
                }
            );
            
        }, 800); // Loading delay
    }

    // Initialize team rendering when document is ready
    $(document).ready(function() {
        renderTeamMembers();
        
        // Optional: Add a refresh button for team data
        if ($('#refreshTeam').length) {
            $('#refreshTeam').on('click', function() {
                renderTeamMembers();
            });
        }
    });

    // Mobile menu toggle function (existing)
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    // Optional: Add search functionality for team members
    function searchTeamMembers(searchTerm) {
        const filteredTeam = teamData.team.filter(member => 
            member.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            member.position.toLowerCase().includes(searchTerm.toLowerCase()) ||
            member.description.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        // Render filtered results
        const teamGrid = $('#teamGrid');
        teamGrid.empty();
        
        if (filteredTeam.length === 0) {
            teamGrid.html('<div class="col-span-full text-center py-8"><p class="text-dark">No team members found matching your search.</p></div>');
            return;
        }
        
        $.each(filteredTeam, function(index, member) {
            // Same card template as above
            const teamMemberCard = `
                <div class="team-member bg-white rounded-lg p-8 text-center shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                    <div class="relative mb-6">
                        <div class="w-32 h-32 mx-auto mb-4 relative overflow-hidden rounded-full border-4 border-primary/20">
                            <img src="${member.image}" alt="${member.name}" class="w-full h-full object-cover" 
                                 onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-lightGray flex items-center justify-center\\'>
                                     <svg class=\\'w-16 h-16 text-primary/50\\' fill=\\'currentColor\\' viewBox=\\'0 0 20 20\\'>
                                         <path fill-rule=\\'evenodd\\' d=\\'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z\\' clip-rule=\\'evenodd\\' />
                                     </svg>
                                 </div>'">
                        </div>
                        <div class="absolute top-2 right-2">
                            <span class="inline-block bg-primary text-white text-xs px-2 py-1 rounded-full">
                                ${member.experience}
                            </span>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2 text-primaryShades-800">${member.name}</h3>
                    <p class="text-primary font-semibold mb-4">${member.position}</p>
                    <p class="text-dark text-sm mb-6 leading-relaxed">${member.description}</p>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="${member.linkedin}" target="_blank" rel="noopener noreferrer" 
                           class="linkedin-link inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors duration-300"
                           title="Connect on LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            `;
            
            teamGrid.append(teamMemberCard);
        });
    }
</script>
<?= $this->endSection() ?>