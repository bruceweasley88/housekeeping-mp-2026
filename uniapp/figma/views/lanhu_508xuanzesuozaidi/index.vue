<template>
  <div class="page flex-col">
    <div class="box_1 flex-col">
      <div class="text-wrapper_1 flex-row"><span class="text_1">选择您的所在地</span></div>
      <div class="text-wrapper_2 flex-row"><span class="text_2">茂名市</span></div>
      <div class="text-wrapper_3 flex-row"><span class="text_3">珠海市</span></div>
      <div class="text-wrapper_4 flex-row">
        <span class="text_4">广东省</span>
        <span class="text_5">广州市</span>
        <span class="text_6">天河区</span>
      </div>
      <div class="group_1 flex-row">
        <div class="list_1 flex-row">
          <div class="text-wrapper_5 flex-col justify-between" v-for="(item, index) in loopData0" :key="index">
            <span class="text_7" v-html="item.lanhutext0"></span>
            <span class="text_8" v-html="item.lanhutext1"></span>
          </div>
        </div>
      </div>
      <div class="text-wrapper_6 flex-row justify-between">
        <span class="text_9">取消</span>
        <span class="text_10">确定</span>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loopData0: [
        { lanhutext0: '惠州市', lanhutext1: '江门市' },
        { lanhutext0: '海珠区', lanhutext1: '越秀区' }
      ],
      constants: {}
    };
  },
  methods: {}
};
</script>
<style scoped lang="scss" src="./assets/index.scss" />