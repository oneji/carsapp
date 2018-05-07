<template>
  <div>
    <v-layout row wrap>
      <v-flex xs12 sm12 md12 lg12>
        <v-btn @click.native.stop="dialog = true" color="success">Создать компанию</v-btn>
        <!-- Create new company modal -->
        <v-dialog v-model="dialog" max-width="500">
          <v-card>
            <v-card-title class="headline">Создать компанию</v-card-title>
            <v-card-text>
              <v-layout>
                <v-flex xs12>
                  <form>
                    <v-text-field type="text" v-model="newCompany.company_name" name="company_name" label="Название компании" prepend-icon="business"                 
                      v-validate="'required'" 
                      data-vv-name="company_name" data-vv-as='"Название компании"' required
                      :error-messages="errors.collect('company_name')"
                      ></v-text-field>
                    <v-text-field hint="Например: +992 987641312" prepend-icon="phone"  v-model="newCompany.contact" name="contact" label="Контакт компании"></v-text-field>

                    <img class="logo-preview" :src="newCompany.logo.url" height="150" v-if="newCompany.logo.url" />
                    <v-text-field label="Выберите логотип" @click="pickFile" v-model="newCompany.logo.name" prepend-icon="attach_file"></v-text-field>
                    <input type="file" style="display: none" @change="onFilePicked" ref="image" accept="image/*">
                  </form>
                </v-flex>
              </v-layout>
            </v-card-text>
            
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat="flat" @click.native="dialog = false">Закрыть</v-btn>
              <v-btn color="green darken-1" :loading="loading.button" flat="flat" @click.native="createCompany">Создать</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-flex>
    </v-layout>    
    
    <!-- A list of companies -->
    <transition-group tag="v-layout" class="row wrap" name="slide-x-transition">     
      <v-flex xs12 sm4 md3 lg2 v-for="item in items" :key="item.id">
        <v-card>
          <v-card-media height="150px">
            <v-layout row justify-center align-center>
              <v-flex xs6 sm6 md6 lg6>
                <img v-if="item.logo" :src="`${assetURL}/uploads/logos/companies/${item.logo}`" :alt="`Логотип ${item.company_name}`">
                <img v-else src="/images/no-logo.png" alt="Нет логотипа">
              </v-flex>
            </v-layout>
          </v-card-media>            
          <v-card-title primary-title>
            <div>
              <h3 class="headline mb-0">{{ item.company_name }}</h3>
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
import DataTable from '~/components/DataTable'
import axios from 'axios'

export default {
  head: {
    title: 'Компании'
  },
  $_veeValidate: {
    validator: 'new'
  },
  components: {
    DataTable
  }, 
  computed: {
    assetURL() {
      return process.env.apiURL || null;
    },
  },
  data() {
    return {
      // Controls
      selected: [],
      search: '',
      dialog: false,  
      loading: {
        table: false,
        button: false
      },    
      alert: {
        noCompanies: false
      },

      // API
      headers: [
        { text: 'Логотип', align: 'left', sortable: false, value: 'logo' },
        { text: 'Компания', align: 'left', value: 'company_name' },
        { text: 'Контакт', aling: 'left', value: 'contact' },
      ],
      items: [],
      newCompany: {
        company_name: '',
        contact: '',
        logo: {
          name: '',
          file: '',
          url: ''
        },
      }
    }
  },
  methods: {
    fetchCompanies() {
      this.alert.noCompanies = false;
      this.$axios.get('/admin/companies')
        .then(response => {
          this.items = response.data;
          if(this.items.length === 0) {
            this.alert.noCompanies = true;
          }
        })
        .catch(error => {

        });
    },

    createCompany() {
      this.$validator.validateAll()
        .then(success => {
          if(success) {
            let formData = new FormData();
            formData.append('company_name', this.newCompany.company_name);
            formData.append('contact', this.newCompany.contact);
            formData.append('logo', this.newCompany.logo.file);

            this.$axios.post('/admin/companies', formData)
            .then(response => {
              this.items.push(response.data);
              this.alert.noCompanies = false;
            })
            .catch(error => {

            });
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
  }
}
</script>

<style>
  .logo-preview {
    display: block;
    margin: 0 auto;
  }
</style>
