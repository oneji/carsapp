<template>
  <div>
    <v-layout row wrap>
      <v-flex xs12 sm12 md12 lg12>
        <v-btn @click.native.stop="dialog.createC = true" color="success" v-can="['сreate-company']">Создать компанию</v-btn>
        <v-btn @click.native.stop="dialog.bindUser = true" color="info" v-can="['bind-user-to-company']">Привязать пользователя</v-btn>
        <!-- Create new company modal -->
        <v-dialog v-model="dialog.createC" max-width="500">
          <form @submit.prevent="createCompany" data-vv-scope="create-company-form">
            <v-card>
              <v-card-title class="headline">Создать компанию</v-card-title>
              <v-card-text>
                <v-layout>
                  <v-flex xs12>                    
                    <v-text-field type="text" v-model="newCompany.company_name" name="company_name" label="Название компании" prepend-icon="business"                 
                      v-validate="'required'" 
                      data-vv-name="company_name" data-vv-as='"Название компании"' required
                      :error-messages="errors.collect('company_name')"
                      ></v-text-field>
                    <v-text-field hint="Например: +992 987641312" prepend-icon="phone"  v-model="newCompany.contact" name="contact" label="Контакт компании"></v-text-field>

                    <img class="logo-preview" :src="newCompany.logo.url" height="150" v-if="newCompany.logo.url" />
                    <v-text-field label="Выберите логотип" @click="pickFile" v-model="newCompany.logo.name" prepend-icon="attach_file"></v-text-field>
                    <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">                    
                  </v-flex>
                </v-layout>
              </v-card-text>
              
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" flat="flat" @click.native="dialog.createC = false">Закрыть</v-btn>
                <v-btn color="green darken-1" :loading="loading.button" flat="flat" type="submit">Создать</v-btn>
              </v-card-actions>
            </v-card>
          </form>
        </v-dialog>
        <!-- Bind user company modal -->
        <v-dialog v-model="dialog.bindUser" max-width="500">
          <form @submit.prevent="bindUserToCompany" data-vv-scope="bind-user-form">
            <v-card>
              <v-card-title class="headline">Привязать пользователя</v-card-title>
              <v-card-text>
                <v-layout>
                  <v-flex xs12>                    
                    <v-select :items="users" v-model="bindUser.user_id" label="Выберите пользователя" prepend-icon="person" persistent-hint
                      name="user_id"
                      v-validate="'required'" 
                      :error-messages="errors.collect('user_id')"
                      data-vv-name="user_id" data-vv-as='"Пользователь"'
                    ></v-select>

                    <v-select 
                      :items="companies" 
                      v-model="bindUser.companies" 
                      label="Выберите компанию" 
                      prepend-icon="business" 
                      persistent-hint
                      name="company_id" multiple
                    ></v-select>

                    <v-select 
                      :items="stos" 
                      v-model="bindUser.stos" 
                      label="Выберите СТО" 
                      prepend-icon="business" 
                      persistent-hint
                      name="sto_id" multiple
                    ></v-select>
                  </v-flex>
                </v-layout>
              </v-card-text>
              
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" flat="flat" @click.native="dialog.bindUser = false">Закрыть</v-btn>
                <v-btn color="green darken-1" :loading="loading.button" flat="flat" type="submit">Привязать</v-btn>
              </v-card-actions>
            </v-card>
          </form>
        </v-dialog>

        <v-snackbar :timeout="snackbar.timeout" :color="snackbar.color" v-model="snackbar.active">
          {{ snackbar.text }}
          <v-btn dark flat @click.native="snackbar.active = false">Закрыть</v-btn>
        </v-snackbar>
      </v-flex>
    </v-layout>

    <v-layout v-if="loading.page" style="position: relative">
      <Loading :loading="loading.page" />
    </v-layout>
    
    <!-- A list of companies -->
    <transition-group tag="v-layout" class="row wrap" name="slide-x-transition" v-if="!loading.page">
      <v-flex xs12 sm4 md3 lg3 v-for="item in items.companies" :key="item.id">
        <v-card>
          <v-card-media height="150px">
            <v-layout row justify-center align-center>
              <v-flex xs6 sm6 md6 lg6>
                <img v-if="item.logo" :src="`${assetURL}/${item.logo}`" :alt="`Логотип ${item.company_name}`">
                <img v-else src="/static/images/no-logo.png" alt="Нет логотипа">
              </v-flex>
            </v-layout>
          </v-card-media> 
          <v-divider></v-divider>           
          <v-card-title primary-title>
            <div>
              <h3 class="subheading mb-0">{{ item.company_name }}</h3>
              <div v-if="item.contact">{{ item.contact }}</div>
              <div v-else>Контакта нет</div>
            </div>
          </v-card-title>
          <!-- <v-card-actions>
            <v-btn flat color="orange">Изменить</v-btn>
          </v-card-actions> -->
        </v-card>
      </v-flex>      
    </transition-group>

    <!-- Alert if there're no companies registered -->
    <v-layout v-if="alert.noCompanies">
      <v-flex>
        <v-alert outline transition="scale-transition" type="info" :value="true">
          Ни одной компании не зарегистрировано.
        </v-alert>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
import axios from '@/axios'
import config from '@/config'
import Loading from '@/components/Loading'

export default {
    $_veeValidate: {
      validator: 'new'
    },
    components: {
      Loading
    },
    computed: {
      assetURL() {
        return config.assetsURL;
      },
    },
    data() {
      return {
        // Controls
        selected: [],
        dialog: {
          createC: false,
          bindUser: false
        },  
        loading: {
          page: true,
          button: false
        },    
        alert: {
          noCompanies: false
        },
        snackbar: {
          active: false,
          text: '',
          timeout: 5000,
          color: ''
        },
        bindUser: {
          user_id: null,
          companies: [],
          stos: []
        },

        // API
        items: [],
        newCompany: {
          company_name: '',
          contact: '',
          logo: {
            name: '',
            file: '',
            url: ''
          },
        },
        users: [],
        companies: [],
        stos: []
      }
  },
  methods: {
      fetchCompanies() {
          axios.get('/admin/companies')
              .then(response => {
                this.items = response.data;

                this.items.companies.map(company => {
                  this.companies.push({
                    text: company.company_name,
                    value: company.id
                  });
                });

                this.items.stos.map(sto => {
                  this.stos.push({
                    text: sto.sto_name,
                    value: sto.id
                  })
                });

                if(this.items.companies.length === 0) {
                  this.alert.noCompanies = true;
                }

                this.loading.page = false;
              })
              .catch(error => {
                console.log(error);
              });
      },

      fetchUsers() {
        axios.get('/admin/users')
          .then(response => {
            if(response.status === 200) {
              let fullUsersInfo = response.data;

              fullUsersInfo.map(user => {
                this.users.push({
                  text: user.fullname,
                  value: user.id
                });                
              })
            }
          })
          .catch(error => console.log(error));
      },

      createCompany() {
        this.loading.button = true;
        this.$validator.validateAll('create-company-form')
          .then(success => {
            if(success) {
              let formData = new FormData();
              formData.append('company_name', this.newCompany.company_name);
              formData.append('contact', this.newCompany.contact);
              formData.append('logo', this.newCompany.logo.file);

              axios.post('/admin/companies', formData)
                .then(response => {
                  this.loading.button = false;
                  this.items.push(response.data.company);
                  this.companies.push({
                    text: response.data.company.company_name,
                    value: response.data.company.id
                  });                  
                  this.alert.noCompanies = false;
                  this.snackbar.color = 'success';
                  this.snackbar.text = response.data.message;
                  this.snackbar.active = true;
                })
                .catch(error => console.log(error));
            } else {
              this.loading.button = false;
            }
          })
      },

      bindUserToCompany() {
        this.loading.button = true;
        this.$validator.validateAll('bind-user-form')
          .then(success => {
            if(success) {
              axios.post(`/admin/companies/bind/${this.bindUser.user_id}`, {
                'companies': this.bindUser.companies,
                'stos': this.bindUser.stos
              }).then(response => {
                console.log(response.data);
                
                if(response.status === 200) {
                  this.loading.button = false;
                  this.snackbar.color = 'success';  
                  this.snackbar.text = response.data.message;
                  this.snackbar.active = true;
                  this.bindUser.companies = [];
                }
              }).catch(error => console.log(error));
            } else {
              this.loading.button = false;
            }
          })       
      },  

      pickFile () {
        this.$refs.image.click();
      },

      onFilePicked (e) {
        const files = e.target.files;
        if(files[0] !== undefined) {
          this.newCompany.logo.name = files[0].name;
          if(this.newCompany.logo.name.lastIndexOf('.') <= 0) {
            return
          }

          const fr = new FileReader();
          fr.readAsDataURL(files[0])
          fr.addEventListener('load', () => {
            this.newCompany.logo.url = fr.result;
              this.newCompany.logo.file = files[0];
              })
          } else {
            this.newCompany.logo.url = '';
              this.newCompany.logo.name = '';
              this.newCompany.logo.file = '';
        }
      }
  },
  created() {
    this.fetchCompanies();
    this.fetchUsers();
  }
}
</script>

<style>
  .logo-preview {
    display: block;
    margin: 0 auto;
  }
</style>
