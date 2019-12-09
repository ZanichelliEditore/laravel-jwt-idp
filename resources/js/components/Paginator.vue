<template>
    <nav aria-label="User navigation list">
        <ul class="pagination">
            <li v-if="pagination.current_page !== 1" class="page-item" @click.prevent="prevPage">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-for="page in pages_to_render" :class="['page-item', pagination.current_page === page ? 'active' : null]" @click.prevent="changePage(page)">
                <a class="page-link" href="#">{{ page }}</a>
            </li>
            <li v-if="pagination.current_page !== pagination.last_page" class="page-item" @click.prevent="nextPage">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "Paginator",

        props: {
            pagination: {
                required: true,
                type: Object
            },
            onChangePage: {
                required: true,
                type: Function
            }
        },

        data() {
            return {
                pages_to_render: []
            }
        },

        created() {
            this.makePagination();
        },

        watch: {
            pagination: function(newValue, oldValue) {
                this.makePagination();
            }
        },

        methods: {
            makePagination() {

                let startPage = (this.pagination.current_page - 2) < 1 ? 1 : (this.pagination.current_page - 2);
                let endPage = (this.pagination.current_page + 2) > this.pagination.last_page ? this.pagination.last_page : (this.pagination.current_page + 2);
                let pageToRender = [];


                for(let i=startPage, j=0; i<=this.pagination.last_page && j<5; i++, j++){
                    pageToRender.push(i);
                }

                if(this.pagination.last_page > 5 && pageToRender.length < 5) {

                    for(let i=pageToRender[0] - 1; pageToRender.length < 5 && i>1; i--){
                        pageToRender.unshift(i);
                    }

                }

                this.pages_to_render = pageToRender;
            },

            prevPage() {
                const prevPage = this.pagination.current_page - 1;

                if (this.pagination.current_page !== prevPage && prevPage > 0) {
                    this.changePage(prevPage);
                }
            },

            nextPage() {
                const nextPage = this.pagination.current_page + 1;

                if (this.pagination.current_page !== nextPage && nextPage < this.pagination.last_page + 1) {
                    this.changePage(nextPage);
                }
            },

            changePage(page) {
                this.onChangePage(page);
            }
        }
    }
</script>

<style scoped>

</style>