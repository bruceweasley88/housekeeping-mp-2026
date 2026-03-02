<template>
  <div class="page flex-col">
    <div class="group_1 flex-col">
      <div class="group_2 flex-col justify-center">
        <div class="group_3 flex-row">
          <div class="image-text_1 flex-col justify-between">
            <img
              class="image_1"
              referrerpolicy="no-referrer"
              src="./assets/img/FigmaDDSSlicePNG122234dd450dc05eb2a6d72c708bb29a.png"
            />
            <span class="text-group_1">首页</span>
          </div>
          <div class="image-text_2 flex-col justify-between">
            <img
              class="image_2"
              referrerpolicy="no-referrer"
              src="./assets/img/FigmaDDSSlicePNG9fab2cc21c7fb22b045d1baec26ef3c8.png"
            />
            <span class="text-group_2">互助</span>
          </div>
          <div class="image-text_3 flex-col justify-between">
            <img
              class="thumbnail_1"
              referrerpolicy="no-referrer"
              src="./assets/img/FigmaDDSSlicePNG5c952e3bd74c184eb87fcda37da59373.png"
            />
            <span class="text-group_3">消息</span>
          </div>
          <div class="group_4 flex-col justify-between">
            <div class="group_5 flex-col"></div>
            <span class="text_1">我的</span>
          </div>
          <div class="group_6 flex-col"></div>
        </div>
      </div>
      <div class="group_7 flex-col">
        <div class="section_1 flex-row">
          <span class="text_2">17:41</span>
          <div class="group_8 flex-col"></div>
          <div class="group_9 flex-col"></div>
          <img
            class="thumbnail_2"
            referrerpolicy="no-referrer"
            src="./assets/img/FigmaDDSSlicePNGd52eb7edc3332703a2debc88b3f73bbb.png"
          />
          <span class="text_3">82%</span>
        </div>
        <div class="section_2 flex-row justify-between">
          <span class="text_4">互助</span>
          <img
            class="image_3"
            referrerpolicy="no-referrer"
            src="./assets/img/FigmaDDSSlicePNGac40403b497a3e3c5b2e8df94903cc8b.png"
          />
        </div>
        <div class="section_3 flex-row justify-between">
          <div class="box_1 flex-row">
            <div class="section_4 flex-col">
              <div class="text-wrapper_1 flex-row justify-between">
                <span class="text_5">26</span>
                <span class="text_6">次</span>
              </div>
              <span class="text_7">发布需求</span>
            </div>
            <div class="section_5 flex-col"></div>
            <div class="section_6 flex-col"></div>
            <div class="section_7 flex-col"></div>
          </div>
          <div class="box_2 flex-col">
            <div class="box_3 flex-row">
              <span class="text_8">0</span>
              <span class="text_9">次</span>
              <div class="section_8 flex-col"></div>
            </div>
            <div class="text-wrapper_2 flex-row"><span class="text_10">承接需求</span></div>
            <div class="box_4 flex-col"></div>
          </div>
        </div>
        <div class="section_9 flex-row">
          <div class="group_10 flex-col"></div>
          <span class="text_11">我发布的</span>
          <span class="text_12">我承接的</span>
        </div>
        <div class="section_10 flex-row">
          <div class="text-wrapper_3 flex-col"><span class="text_13">全部需求</span></div>
          <div class="text-wrapper_4 flex-col"><span class="text_14">发布中</span></div>
          <div class="text-wrapper_5 flex-col"><span class="text_15">已完成</span></div>
          <div class="text-wrapper_6 flex-col"><span class="text_16">已下架</span></div>
        </div>
        <div class="list_1 flex-col">
          <div class="list-items_1 flex-col" v-for="(item, index) in loopData0" :key="index">
            <div class="group_11 flex-row">
              <div class="text-wrapper_7 flex-col"><span class="text_17" v-html="item.lanhutext0"></span></div>
              <span class="text_18" v-html="item.lanhutext1"></span>
              <span class="text_19" v-html="item.lanhutext2"></span>
            </div>
            <div class="image-text_4 flex-row justify-between">
              <div class="text-group_4 flex-col justify-between">
                <span class="text_20" v-html="item.lanhutext3"></span>
                <div class="text-wrapper_8 flex-row justify-between">
                  <span class="text_21" v-html="item.lanhutext4"></span>
                  <span class="text_22" v-html="item.lanhutext5"></span>
                </div>
              </div>
              <img class="image_4" referrerpolicy="no-referrer" :src="item.lanhuimage0" />
            </div>
            <div class="text-wrapper_9 flex-row justify-between">
              <span class="text_23" v-html="item.lanhutext6"></span>
              <span class="text_24" :style="{ color: item.lanhufontColor7 }" v-html="item.lanhutext7"></span>
            </div>
          </div>
        </div>
        <div class="section_11 flex-col"></div>
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
          lanhutext0: '紧急',
          lanhutext1: '小三数学家教2小时',
          lanhutext2: '保利天悦A10',
          lanhutext3: '需要找小学三年级数学家教，周六日两天<br/>晚上19:00-21:00上课，合适的可长期…',
          lanhutext4: '300',
          lanhutext5: '元/小时',
          lanhuimage0: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
          lanhutext6: '发布于2026.01.02&nbsp;12:00',
          lanhutext7: '发布中',
          lanhufontColor7: 'rgba(0,186,184,1.000000)'
        },
        {
          lanhutext0: '紧急',
          lanhutext1: '小三数学家教2小时',
          lanhutext2: '保利天悦A10',
          lanhutext3: '需要找小学三年级数学家教，周六日两天<br/>晚上19:00-21:00上课，合适的可长期…',
          lanhutext4: '300',
          lanhutext5: '元/小时',
          lanhuimage0: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
          lanhutext6: '发布于2026.01.02&nbsp;12:00',
          lanhutext7: '已完成',
          lanhufontColor7: 'rgba(189,189,189,1.000000)'
        },
        {
          lanhutext0: '紧急',
          lanhutext1: '小三数学家教2小时',
          lanhutext2: '保利天悦A10',
          lanhutext3: '需要找小学三年级数学家教，周六日两天<br/>晚上19:00-21:00上课，合适的可长期…',
          lanhutext4: '300',
          lanhutext5: '元/小时',
          lanhuimage0: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
          lanhutext6: '发布于2026.01.02&nbsp;12:00',
          lanhutext7: '已下架',
          lanhufontColor7: 'rgba(189,189,189,1.000000)'
        }
      ],
      constants: {}
    };
  },
  methods: {}
};
</script>
<style scoped lang="scss" src="./assets/index.scss" />