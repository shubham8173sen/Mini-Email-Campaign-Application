<template>
  <div class="campaign-form">
    <h2>Create New Campaign</h2>
    <form @submit.prevent="submitForm" class="form-container">
      <div class="form-group">
        <label for="name" class="form-label">Campaign Name:</label>
        <input
          type="text"
          id="name"
          v-model="form.name"
          class="form-input"
          :class="{ 'input-error': errors.name }"
          placeholder="Enter campaign name"
        />
        <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
      </div>

      <div class="form-group">
        <label for="csv_file" class="form-label">Upload CSV File:</label>
        <input
          type="file"
          id="csv_file"
          @change="handleFileUpload"
          class="form-input"
          :class="{ 'input-error': errors.csv_file }"
        />
        <span v-if="errors.csv_file" class="error-message">{{ errors.csv_file }}</span>
      </div>

      <div v-if="generalErrors.length" class="error-container">
        <ul class="error-list">
          <li v-for="error in generalErrors" :key="error" class="error-item">{{ error }}</li>
        </ul>
      </div>

      <button type="submit" class="submit-button">Create Campaign</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        csv_file: null,
      },
      errors: {},
      generalErrors: [],
    };
  },
  methods: {
    handleFileUpload(event) {
      this.form.csv_file = event.target.files[0];
    },
    async submitForm() {
      this.errors = {};
      this.generalErrors = [];

      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('csv_file', this.form.csv_file);

      try {
        await axios.post('/api/campaigns', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        alert('Campaign created successfully.');
        // Clear form after successful submission
        this.form.name = '';
        this.form.csv_file = null;
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.generalErrors.push('An unexpected error occurred.');
        }
      }
    },
  },
};
</script>

<style scoped>
.campaign-form {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.h2 {
  text-align: center;
  margin-bottom: 20px;
}

.form-container {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

.form-label {
  margin-bottom: 5px;
  font-weight: bold;
}

.form-input {
  width: 100%;
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

.input-error {
  border-color: #e74c3c;
}

.error-message {
  color: #e74c3c;
  font-size: 0.875em;
  margin-top: 5px;
}

.error-container {
  margin-bottom: 15px;
  background-color: #f8d7da;
  padding: 10px;
  border-radius: 4px;
}

.error-list {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.error-item {
  color: #e74c3c;
}

.submit-button {
  padding: 10px 15px;
  background-color: #3498db;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.submit-button:hover {
  background-color: #2980b9;
}
</style>
