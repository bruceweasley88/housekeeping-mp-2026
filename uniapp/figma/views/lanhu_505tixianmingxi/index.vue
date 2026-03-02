<template>
  <div class="page flex-col">
    <div class="box_1 flex-col">
      <div class="box_2 flex-row">
        <span class="text_1">17:41</span>
        <div class="block_1 flex-col"></div>
        <div class="block_2 flex-col"></div>
        <img
          class="thumbnail_1"
          referrerpolicy="no-referrer"
          src="./assets/img/FigmaDDSSlicePNGd52eb7edc3332703a2debc88b3f73bbb.png"
        />
        <span class="text_2">82%</span>
      </div>
      <div class="box_3 flex-row">
        <div class="group_1 flex-col"></div>
        <span class="text_3">提现明细</span>
        <img
          class="image_1"
          referrerpolicy="no-referrer"
          src="./assets/img/FigmaDDSSlicePNG9ad4e0f795269804f486579f824f4df8.png"
        />
      </div>
    </div>
    <div class="box_4 flex-col">
      <div class="list_1 flex-col">
        <div class="list-items_1 flex-row" v-for="(item, index) in loopData0" :key="index">
          <div class="text-group_1 flex-col justify-between">
            <span class="text_4" v-html="item.lanhutext0"></span>
            <span class="text_5" v-html="item.lanhutext1"></span>
          </div>
          <div class="text-group_2 flex-col justify-between">
            <span class="text_6" v-html="item.lanhutext2"></span>
            <span class="text_7" :style="{ color: item.lanhufontColor3 }" v-html="item.lanhutext3"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loopData0: [
        {
          lanhutext0: '提现',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '-¥130.00',
          lanhutext3: '已完成',
          lanhufontColor3: 'rgba(153,153,153,1.000000)'
        },
        {
          lanhutext0: '提现',
          lanhutext1: '2026.01.01&nbsp;23:00',
          lanhutext2: '-¥130.00',
          lanhutext3: '审核中',
          lanhufontColor3: 'rgba(255,126,34,1.000000)'
        }
      ],
      constants: {}
    };
  },
  methods: {}
};
</script>
<style scoped lang="scss" src="./assets/index.scss" />