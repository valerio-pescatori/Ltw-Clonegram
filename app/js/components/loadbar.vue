<template>
    <div class="fixed gradient-bg-main" :style="'height: '+height+'px; width: '+percentage+'%; '">
    </div>
</template>

<script>
module.exports = {
    props: {
        percentage: 0,
        height: 1,
    }
}
</script> 
