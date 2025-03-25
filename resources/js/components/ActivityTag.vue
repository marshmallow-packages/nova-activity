<template>
    <span
        class="tag"
        :style="{ backgroundColor: backgroundColor, color: color }"
    >
        <Icon v-if="icon" :name="icon" type="micro" />
        <span>{{ name }}</span>
    </span>
</template>

<script>
    import { Icon } from "laravel-nova-ui";

    export default {
        props: ["field", "tag"],

        components: { Icon },

        created() {
            // console.log(this.comment.meta.tags);
        },

        methods: {
            tagSetting() {
                return this.field.actions.find((a) => a.key === this.tag.key);
            },
        },

        computed: {
            name() {
                let action = this.tagSetting();
                return action.name;
            },
            backgroundColor() {
                let action = this.tagSetting();
                return action.background_color;
            },
            color() {
                let action = this.tagSetting();
                return action.color;
            },
            icon() {
                let action = this.tagSetting();
                return action.icon;
            },
        },
    };
</script>
<style>
    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1;
        text-transform: uppercase;
    }
</style>
