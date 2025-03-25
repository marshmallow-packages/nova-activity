<template>
    <div>
        <ActivityTag
            v-for="tag in comment.meta.tags"
            v-bind:key="tag.key"
            :tag="tag"
            :field="field"
        />
    </div>
</template>

<script>
    import ActivityTag from "./ActivityTag";
    import { Icon } from "laravel-nova-ui";

    export default {
        props: ["resourceName", "resourceId", "field", "comment", "hidden"],
        components: { ActivityTag, Icon },
    };
</script>
