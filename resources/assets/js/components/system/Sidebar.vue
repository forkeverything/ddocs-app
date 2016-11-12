<template>
    <div id="sidebar" v-show="showSidebar">
        <button type="button" class="close btn-close-sidebar"
                aria-label="Close" @click="toggleSidebar"
        >
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="sidebar-logo">
            <router-link to="/">
                <img alt="Brand" src="/images/logo/logo-white.svg">
            </router-link>
        </div>
        <div id="side-checklists" class="links-section">
            <h5 class="header">Checklists</h5>
            <router-link to="/checklists/make" class="link-add">
                <button type="button" class="btn btn-text">+</button>
            </router-link>
            <ul id="list-side-checklist" class="list-unstyled list-links" v-if="recentChecklists">
                <li v-for="checklist in recentChecklists">
                    <router-link :to="'/c/' + checklist.hash" class="truncate"><i class="fa fa-list"></i>{{ checklist.name }}
                    </router-link>
                </li>
                <li>
                    <router-link to="/checklists" class="meta">View All</router-link>
                </li>
            </ul>
        </div>
        <div id="side-projects" class="links-section">
            <h5 class="header">Projects</h5>
            <a href="#" class="link-add" @click.prevent="toggleAddProjectForm">
                <button type="button" class="btn btn-text"><span v-show="! addProjectForm">+</span><span v-show="addProjectForm">-</span></button>
            </a>
            <form id="form-add-project" @submit.prevent="startNewProject" v-show="addProjectForm">
                <form-errors @got-error="gotError" stealth="true"></form-errors>
                <input type="text" class="form-control" v-model="newProject" placeholder="New Project Name" ref="projectInput" @blur="startNewProject">
                <span class="help-block" v-if="formErrors.name">
                            {{ formErrors.name[0] }}
                        </span>
            </form>
            <input id="input-side-project-search"
                   type="text"
                   class="form-control"
                   v-model="projectSearch"
                   placeholder="Search..."
                   v-show="! addProjectForm"
            >
            <div class="projects-wrap">
                <ul id="list-side-projects" class="list-unstyled list-links" v-if="projects">
                    <li v-for="project in filteredProjects">
                        <router-link :to="'/projects/' + project.id" class="truncate"><i class="fa fa-industry"></i>{{ project.name }}
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
    const CatchFormErrors = require('../../mixins/catch-form-errors');
    export default {
        data: function () {
            return {
                ajaxReady: true,
                recentChecklists: '',
                projects: '',
                newProject: '',
                addProjectForm: false,
                projectSearch: ''
            }
        },
        computed: {
            authenticatedUser() {
                return this.$store.state.authenticatedUser;
            },
            showSidebar () {
                return this.$store.state.showSidebar && this.authenticatedUser;
            },
            filteredProjects() {
                if(! this.projectSearch) return this.projects;
                return this.projects.filter((singleProject) => {
                    let name = singleProject.name.toLowerCase();
                    let searchTerm = this.projectSearch.toLowerCase();
                    return name.indexOf(searchTerm) !== -1;
                });
            }
        },
        watch: {
          authenticatedUser(user){
              if(user) this.initialize();
          }
        },
        methods: {
            toggleAddProjectForm() {
                this.addProjectForm = ! this.addProjectForm;
                if(this.addProjectForm) {
                    this.projectSearch = '';
                    this.$nextTick(() => $(this.$refs.projectInput).focus());
                }
            },
            toggleSidebar() {
                this.$store.commit('toggleSidebar');
                this.showOnResize = false;
            },
            checkShowSidebar: _.debounce(function () {
                let windowWidth = $(window).width();
                if (windowWidth < 768 && this.showSidebar) this.toggleSidebar();
                if (windowWidth >= 768 && !this.showSidebar) this.toggleSidebar();
            }, 150),
            fetchRecentChecklists() {
                this.$http.get('/api/checklists/recent', {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.recentChecklists = response.json();
                }, (response) => {
                    // error
                    console.log('Error fetching from: /api/checklists/recent');
                });
            },
            fetchProjects(){
                this.$http.get('/api/projects', {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    // success
                    this.projects = response.json();
                }, (response) => {
                    // error
                    console.log('Error fetching from: /api/projects');
                });
            },
            startNewProject(){
                if(! this.newProject) {
                    this.addProjectForm = false;
                    return;
                }
                vueClearValidationErrors();
                this.formErrors = '';
                if (!this.ajaxReady) return;
                this.ajaxReady = false;

                this.$http.post('/api/projects', {
                    name: this.newProject
                }, {
                    before(xhr) {
                        RequestsMonitor.pushOntoQueue(xhr);
                    }
                }).then((response) => {
                    this.projects.unshift(response.json());
                    this.$router.push('/projects/' + response.json().id);
                    this.ajaxReady = true;
                    this.newProject = '';
                    this.addProjectForm = false;
                }, (response) => {
                    // error
                    console.log("error posting to: /projects");
                    this.ajaxReady = true;
                    vueValidation(response);
                });
            },
            initialize() {
                this.fetchRecentChecklists();
                this.fetchProjects();
                this.$nextTick(this.checkShowSidebar);
            }
        },
        mixins: [CatchFormErrors],
        created() {
            window.addEventListener('resize', this.checkShowSidebar);
            vueGlobalEventBus.$on('remove-project', (project) => {
                let index = _.indexOf(this.projects, _.find(this.projects, (singleProject) => singleProject.id === project.id));
                this.projects.splice(index, 1);
            });
        },
        mounted() {
            if(this.authenticatedUser) this.initialize();
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.checkShowSidebar);
            vueGlobalEventBus.$off('remove-project');
        }
    }
</script>