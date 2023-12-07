<template>
    <div
        class="flex flex-col -mx-6 px-6 py-2 space-y-2 md:flex-row @sm/peekable:flex-row @md/modal:flex-row md:py-0 @sm/peekable:py-0 @md/modal:py-0 md:space-y-0 @sm/peekable:space-y-0 @md/modal:space-y-0"
        dusk="field_01"
    >
        <div
            class="md:w-1/4 @sm/peekable:w-1/4 @md/modal:w-1/4 md:py-3 @sm/peekable:py-3 @md/modal:py-3"
        >
            <h4 class="font-normal @sm/peekable:break-all">
                <span>{{ field.name }}</span>
            </h4>
        </div>
        <div
            class="break-all md:w-3/4 @sm/peekable:w-3/4 @md/modal:w-3/4 md:py-3 @sm/peekable:py-3 md/modal:py-3 lg:break-words @md/peekable:break-words @lg/modal:break-words"
        >
            <NovaComments
                :resourceName="resourceName"
                :field="field"
                :resourceId="resourceId"  />
        </div>
    </div>
</template>


<script>
import NovaComments from "./NovaComments";
export default {
    props: ["index", "resource", "resourceName", "resourceId", "field"],
    components: { NovaComments },
    // created() {
    //     console.log(this.field.name);
    // }
};
</script>
