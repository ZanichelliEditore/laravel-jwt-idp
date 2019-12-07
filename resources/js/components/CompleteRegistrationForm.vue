<template>
  <div class="d-flex flex-column align-items-center mt-5">
    <h1 class="mb-5">Completa la registrazione</h1>
    <transition name="fade">
      <form
        v-if="!completed"
        @submit.prevent="confirmRegistration"
        class="col-10 col-md-8 col-lg-3 px-0"
      >
        <div class="form-group">
          <input type="hidden" name="token" :value="token" />
          <label for="input-password">Password</label>
          <div class="input-group">
            <input
              v-model="password"
              :type="[showPassword ? 'text' : 'password']"
              :class="['form-control', valid ? '' : 'is-invalid']"
              id="input-password"
              placeholder="Inserisci una password sicura"
              @input="validate"
            />
            <div class="input-group-append">
              <button @click="showHidePassword" class="btn btn-outline-secondary" type="button">
                <img style="width: 80%" v-if="showPassword" src="../../images/eye-off.png" />
                <img style="width: 80%" v-else src="../../images/eye.png" />
              </button>
            </div>
          </div>
          <div>
            <small
              :class="[valid ? 'text-muted' : 'text-danger']"
            >Deve contenere almeno 5 caratteri e non può iniziare o terminare con uno spazio</small>
          </div>
        </div>

        <button :disabled="!valid" type="submit" class="btn btn-primary">Conferma</button>
      </form>
      <div v-if="completed" class="col-10 col-md-8 col-lg-3">
        <div class="alert alert-success" role="alert">Registrazione terminata con successo</div>
        <div>
          <a href="/loginForm" role="button" class="btn btn-primary">Accedi</a>
        </div>
      </div>
    </transition>
    <div
      v-if="error"
      class="alert alert-danger col-10 col-md-8 col-lg-3 mt-3"
      role="alert"
    >Si è verificato un errore durante l'attivazione</div>
  </div>
</template>

<script>
export default {
  name: "CompleteRegistrationForm",

  props: {
    token: {
      required: true,
      type: String
    }
  },

  data() {
    return {
      showPassword: false,
      password: null,
      valid: true,
      completed: false,
      error: false
    };
  },

  methods: {
    showHidePassword() {
      this.showPassword = !this.showPassword;
    },

    validate() {
      let regExp = new RegExp("^(?!\\s).{4,}[^\\s]$");
      return (this.valid = regExp.test(this.password));
    },

    confirmRegistration() {
      this.error = false;

      if (!this.validate()) {
        this.valid = false;
        return;
      }

      this.valid = true;
      let vm = this;
      axios
        .post("/v1/complete-registration", {
          token: vm.token,
          password: vm.password
        })
        .then(response => {
          vm.completed = true;
        })
        .catch(error => {
          vm.error = true;
        });
    }
  }
};
</script>

<style scoped>
.fade-enter-active {
  transition: opacity 0.5s;
  transition-delay: 0.5s;
}
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>