<template>
  <form @submit.prevent="submitForm">
    <input v-model="campaignName" type="text" placeholder="Campaign Name" required />
    <input @change="onFileChange" type="file" required />
    <button type="submit">Create Campaign</button>
  </form>
</template>

<script>
export default {
  data() {
    return {
      campaignName: '',
      csvFile: null,
    };
  },
  methods: {
    onFileChange(e) {
      this.csvFile = e.target.files[0];
    },
    async submitForm() {
      const formData = new FormData();
      formData.append('campaign_name', this.campaignName);
      formData.append('csv_file', this.csvFile);

      await axios.post('/campaigns', formData);
    },
  },
};
</script>
