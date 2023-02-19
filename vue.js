new Vue({
    el: "#root",
    data: {
        todoList: [],

        totalNum: "",
        addAlertShow: false,
        editAlertShow: false,

        inputTitle: "",
        inputContent: "",
        currentClickIndex: "",

        newTitle: "",
        newContent: "",

        inputSearch: "",

        currentPage: 1,
        todoPerPage: 5,

        apiUrl: './DatabaseHandler.php'
    },

    mounted() {
        this.getToDoList();
    },

    computed: {
        paginatedTodo() {
            const start = (this.currentPage - 1) * this.todoPerPage;
            const end = start + this.todoPerPage;
            return this.todoList.slice(start, end)
        },

        // 合計ページ数
        totalPages() {
            return Math.ceil(this.todoList.length / this.todoPerPage);
        }
    },

    methods: {
        // データを渡す
        postData(url, data) {
            axios.post(url, data)
                .then(res => {
                    console.log(res.data);
                }).catch(err => {
                    console.log(err);
                }).then(() => {
                    this.getToDoList()
                })
        },
        // データを獲得する
        getToDoList() {
            axios.get(this.apiUrl + '?action=getData')
                .then((res) => {
                    this.todoList = res.data;
                    // console.log(this.todoList);
                })
                .catch(err => {
                    console.log(err);
                })
        },

        // Todoの新規作成
        addTodo() {
            this.addAlertShow = true
        },

        addSureClick() {
            if (this.newTitle && this.newContent) {
                let addData = {
                    title: this.newTitle,
                    content: this.newContent,
                }
                this.postData(this.apiUrl + '?action=insertData', addData)
                this.addAlertShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }
        },

        // Todoの編集
        editTodo(item, index) {
            this.editAlertShow = true
            this.inputTitle = item.title
            this.inputContent = item.content
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID)
        },
        // 編集を確認する
        sureClick() {
            if (this.inputTitle && this.inputContent) {
                let updateData = {
                    title: this.inputTitle,
                    content: this.inputContent,
                    id: this.currentClickIndex
                }
                this.postData(this.apiUrl + '?action=updateData', updateData)
                this.editAlertShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }

        },
        // 編集をキャンセルする
        cancelClick() {
            this.addAlertShow = false
            this.editAlertShow = false
        },

        // Todoの削除
        deleteTodo(item, index) {
            // 現在操作しているtodo項目のIDを渡す
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID)

            this.postData(this.apiUrl + '?action=deleteData', {
                id: this.currentClickIndex
            })
        },

        // タイトルでTodoを検索する
        searchToDo() {
            if (this.inputSearch) {
                // 検索したらcurrentPageも更新すること
                this.currentPage = 1
                this.todoList = this.todoList.filter((item) => {
                    return item.title.includes(this.inputSearch)
                })
            } else {
                this.getToDoList()
            }

        },

        setPage(pageNum) {
            this.currentPage = pageNum;
        }

    },

    filters: {
        // 日時データの処理
        dateFormate(value) {
            const date = new Date(value);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            return year + "年" + month + "月" + day + "日";
        }
    }



})