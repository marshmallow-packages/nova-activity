<template>
    <span
        class="tag"
        :style="{
            backgroundColor: backgroundColor,
            color: color,
            borderColor: color,
        }"
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
        align-items: center;
        border-radius: 0.25rem;
        display: inline-flex;
        font-size: 0.6rem;
        font-weight: 600;
        gap: 0.25rem;
        line-height: 1;
        margin-bottom: 0.5rem;
        margin-right: 0.5rem;
        padding: 0.1rem 0.2rem;
        text-transform: uppercase;
        border: 1px solid;
    }
</style>
