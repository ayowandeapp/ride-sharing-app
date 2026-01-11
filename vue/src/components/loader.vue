<template>
  <div class="loader"></div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
.loader {
    display: block;
    position: relative;
  width: 120px;
  height: 32px;
  background: linear-gradient(#000 4px, transparent 0) no-repeat;
  background-position: 0 16px;
  /* border: 4px solid #e5e7eb;
  border-top-color: #000; */
  /* border-radius: 50%; */
  /* animation: spin 1s linear infinite; */
  overflow: hidden;
  box-sizing: border-box;
}
.loader::before{
    content: '';
    position:absolute;
    left:0;
    bottom: 2px;
    width:28px;
    height:28px;
    border-radius:50%;
    background: #FFF;
    border: 6px solid #000;
  box-sizing: border-box;
  animation: ballbns 1.5s linear infinite;

}
@keyframes ballbns {
  0% {
    left:0;
    transform: translateX(-100%);
  }
  100% {
    left:100%;
    transform: translateX(0%);
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
