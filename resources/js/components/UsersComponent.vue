<template>
  <div>
    <div id="users-container" class="pb-4">
      <h1>Crea nuovo utente</h1>

      <form v-on:submit.prevent="submit">
        <div class="form-row justify-content-between">
          <div class="form-group col-lg-6">
            <label for="input-email">Email</label>
            <input
              type="text"
              v-bind:class="'form-control ' + (!validator.email ? 'is-invalid' : '')"
              id="input-email"
              placeholder="mario.rossi@example.com"
              name="email"
              v-model="form.email"
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-lg-6">
            <label for="input-name">Nome</label>
            <input
              type="text"
              :class="'form-control ' + (!validator.name ? 'is-invalid' : '')"
              id="input-name"
              placeholder="Mario"
              name="name"
              v-model="form.name"
            />
          </div>
          <div class="form-group col-lg-6">
            <label for="input-surname">Cognome</label>
            <input
              type="text"
              :class="'form-control ' + (!validator.surname ? 'is-invalid' : '')"
              id="input-surname"
              placeholder="Rossi"
              name="surname"
              v-model="form.surname"
            />
          </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Crea utente</button>
      </form>

      <div class="d-flex mb-2 mt-5 align-items-center justify-content-between">
        <div>
          <h1>Lista utenti</h1>
        </div>
        <div class="col-4 input-group px-0">
          <input
            v-model="filterUserInput"
            type="text"
            class="form-control"
            aria-label="Search input filter"
            placeholder="Filtra per e-mail"
          />
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-dark">
            <tr>
              <th scope="col">ID</th>
              <th scope="col">E-mail</th>
              <th scope="col">Verificato</th>
              <th scope="col">Nome</th>
              <th scope="col">Cognome</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <th colspan="7" class="px-0 py-0">
                <div class="progress" style="height: 5px">
                  <div
                    class="progress-bar progress-bar-striped progress-bar-animated"
                    role="progressbar"
                    aria-valuenow="75"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width: 100%"
                  ></div>
                </div>
              </th>
            </tr>
            <tr v-for="user in pagination.data" :key="user.id">
              <th scope="row">{{ user.id }}</th>
              <td>{{ user.email }}</td>
              <td class="text-center">
                <i v-if="user.is_verified" class="fas fa-check"></i>
              </td>
              <td>{{ user.name }}</td>
              <td>{{ user.surname }}</td>
            </tr>
            <tr v-if="!loading && pagination.data.length === 0">
              <td colspan="7" class="text-center">Nessun utente trovato</td>
            </tr>
          </tbody>
        </table>
      </div>
      <paginator
        v-if="pagination.total > pagination.per_page"
        :pagination="pagination"
        :onChangePage="page => loadUsers(page)"
      ></paginator>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../event-bus";

export default {
  data() {
    return {
      form: {
        email: null,
        name: null,
        surname: null
      },
      validator: {
        email: true,
        password: true,
        name: true,
        surname: true
      },
      pagination: {
        current_page: -1,
        data: [],
        total: 0,
        last_page: 0,
        per_page: 10
      },
      loading: false,
      filterUserInput: null
    };
  },

  created() {
    this.loadUsers();
  },

  watch: {
    filterUserInput: function(newValue, oldValue) {
      this.filterUsers();
    },

  },

  methods: {
    submit: function() {
      if (!this.validate()) {
        return;
      }

      let vm = this;
      axios
        .post("/admin/users", {
          email: vm.form.email,
          name: vm.form.name,
          surname: vm.form.surname
        })
        .then(function(data) {
          vm.resetForm();
          vm.loadUsers();
          EventBus.$emit("newNotification", {
            message: "Utente aggiunto correttamente",
            type: "SUCCESS"
          });
        })
        .catch(function(error) {
          EventBus.$emit("newNotification", {
            message: "Errore durante la registrazione",
            type: "ERROR"
          });
        });
    },

    validate: function() {
      this.validator.email = this.validateEmail(this.form.email);
      this.validator.name = !!this.form.name;
      this.validator.surname = !!this.form.surname;

      if (!this.validateEmail(this.form.email)) {
        return false;
      }

      if (this.form.email && !this.validateEmail(this.form.email)) {
        return false;
      }

      return this.form.email && this.form.name && this.form.surname;
    },

    validateEmail: function validateEmail(email) {
      let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    },

    resetForm: function() {
      this.form.email = null;
      this.form.name = null;
      this.form.surname = null;
    },

    loadUsers(page = 1) {
      let vm = this;
      this.loading = true;

      axios
        .get("/admin/users", {
          params: {
            page: page,
            q: vm.filterUserInput
          }
        })
        .then(response => {
          vm.pagination = response.data;
          vm.loading = false;
        })
        .catch(error => {
          vm.loading = false;
          EventBus.$emit("newNotification", {
            message: "Errore durante il caricamento degli utenti",
            type: "ERROR"
          });
        });
    },

    filterUsers() {
      this.loadUsers();
    }
  }
};
</script>