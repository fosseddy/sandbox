<script>
export default {
  data() {
    return {
      selectarr: [
        { id: 1, value: "google", text: "Google" },
        { id: 2, value: "yandex", text: "Yandex" },
        { id: 3, value: "bing", text: "Bing" }
      ],
      checkboxarr: [
        { id: 1, value: "news", text: "News" },
        { id: 1, value: "tutorials", text: "Tutorials" },
        { id: 1, value: "nothing", text: "Nothing" }
      ],
      radioarr: [
        { id: 1, value: "video", text: "Video Courses" },
        { id: 1, value: "blog", text: "Blogs" },
        { id: 1, value: "other", text: "Other" }
      ],

      name: "",
      age: 1,
      source: "",
      interests: [],
      learnPref: ""
    };
  },

  created() {
    this.source = this.selectarr[0].value;
    this.learnPref = this.radioarr[0].value;
  },

  methods: {
    printValues(e) {
      e.preventDefault();

      console.log(
        this.name,
        this.age,
        this.source,
        this.interests.join(','),
        this.learnPref
      );
    }
  }
}
</script>

<template>
<form @submit="printValues">
  <div>
    <p>Your Name</p>
    <input type="text" v-model="name" />
  </div>

  <div>
    <p>Your Age (years)</p>
    <input type="number" v-model="age" />
  </div>

  <div>
    <p>How did you hear about us?</p>
    <select v-model="source">
      <option v-for="s in selectarr" :key="s.id" :value="s.value">
        {{ s.text }}
      </option>
    </select>
  </div>

  <div>
    <p>What are you interested in?</p>
    <div v-for="c in checkboxarr" :key="c.id">
      <input type="checkbox"
             :id="c.value"
             :value="c.value"
             v-model="interests" />
      <label :for="c.value">{{ c.text }}</label>
    </div>
  </div>

  <div>
    <p>How do you learn?</p>
    <div v-for="r in radioarr" :key="r.id">
      <input type="radio"
             :id="r.value"
             :value="r.value"
             v-model="learnPref" />
      <label :for="r.value">{{ r.text }}</label>
    </div>
  </div>

  <button type="submit">Save Data</button>
</form>
</template>

<style scoped>
* {
  padding: 0;
  margin: 0;
  box-sizing: boreder-box;
}
</style>
