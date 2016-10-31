<template>
    <div id="projects-all" class="container-fluid">
        <h3>Projects</h3>
        <div class="row">
            <div class="col-sm-8">
                <p>
                    Projects are used to keep track of your internal team's files. See what
                    members of your team are working on, view progress and file statuses at a glance.
                </p>
            </div>
        </div>
        <br>
        <div id="create-project" class="dropdown">
            <button type="button" class="btn btn-info" data-toggle="dropdown"><i class="fa fa-plus"></i> Project</button>
            <form id="form-create-project" @submit.prevent="startNewProject" class="dropdown-menu">
                <form-errors @got-error="gotError" stealth="true"></form-errors>
                <div class="form-group"
                     :class="{
                        'has-error': formErrors.name
                     }"
                >
                    <label for="field-project-name">Name</label>
                    <input id="field-project-name" type="text" v-model="name" class="form-control">
                    <span class="help-block" v-if="formErrors.name">
                            {{ formErrors.name[0] }}
                    </span>
                </div>
                <div class="form-group">
                    <label for="field-project-description">Description</label>
                    <textarea id="field-project-description" name="description" class="form-control" v-model="description"></textarea>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-sm">Create</button>
                </div>
            </form>
        </div>
        <br>
        <div id="projects-wrap">
            <rectangle-loader :loading="initializing"></rectangle-loader>
            <ul id="projects-list" class="list-unstyled" v-if="projects">
                <li v-for="project in projects">
                    <router-link :to="'/projects/' + project.id">{{ project.name}}</router-link>
                </li>
            </ul>
            <p class="text-muted" v-if="! projects">
                You haven't created any projects.
            </p>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ProjectsList',
    data: function(){
        return {
            initializing: true,
            ajaxReady: true,
            projects: '',
            name: '',
            description: ''
        }
    },
    methods: {
        startNewProject(){
          vueClearValidationErrors();
          if(!this.ajaxReady) return;
          this.ajaxReady = false;

          this.$http.post('/api/projects', {
              name: this.name,
              description: this.description
          }, {
              before(xhr) {
                  RequestsMonitor.pushOntoQueue(xhr);
              }
          }).then((response) => {
              this.$router.push('/projects/' + response.json().id);
          },(response) => {
              // error
              console.log("error posting to: /projects");
              this.ajaxReady = true;
              vueValidation(response);
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
                this.initializing = false;
            }, (response) => {
                // error
                this.initializing = false;
                console.log('Error fetching from: /api/projects');
            });
        }
    },
    mounted(){
        this.fetchProjects();
    },
    mixins: [
        require('../../mixins/catch-form-errors')
    ]
}
</script>