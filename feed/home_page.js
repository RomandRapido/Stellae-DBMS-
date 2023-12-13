function intializeCheckBox() {
  const defButton = document.getElementById("Title");
  defButton.click();
  const defButton2 = document.getElementById("Recent");
  defButton2.click();
}

isLogin = false;
initialFeed = false;
function initializeLoginStatus(loginConfirm) {
  if (loginConfirm) {
    isLogin = true;
  }
}

const likedStatus = [];

function initializeLikedStatus(index, initialLiked) {
  if (isLogin) {
    likedStatus[index] = initialLiked;
    if (initialLiked) {
      const likeButton = document.getElementById(`likeButton${index}`);
      likeButton.classList.toggle("liked", likedStatus[index]);
    }
  }
}

function toggleLike(index, post_id) {
  if (isLogin) {
    if (typeof likedStatus[index] === "undefined") {
      likedStatus[index] = false;
    }

    const likeButton = document.getElementById(`likeButton${index}`);

    likedStatus[index] = !likedStatus[index];

    likeButton.classList.toggle("liked", likedStatus[index]);

    const likesCountElement = document.getElementById("likesCount" + index);
    const currentLikes = parseInt(likesCountElement.innerText, 10);

    if (likedStatus[index]) {
      likesCountElement.innerText = currentLikes + 1;
    } else {
      likesCountElement.innerText = currentLikes - 1;
    }

    const updateLikeEndpoint = "like_logic.php";
    const formData = new FormData();
    formData.append("post_id", post_id);

    fetch(updateLikeEndpoint, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
      })
      .then((data) => {
        console.log("Like status updated on the server:", data);
      })
      .catch((error) => {
        console.error("Error updating like status:", error);
      });
  } else {
    alert("Please Log in for this feature to be available");
  }
}

let offset = 0;
function updateOffset() {
  var papersLength = document.getElementsByClassName("paper_for_each").length;
  offset = papersLength;
}
searchQuery = "";
function initializeSearchQuery(customQuery) {
  searchQuery = customQuery;
}

let checkedFilterGlobal = ["Title"];
let searchVal = "";
let radioVal = "Recent";
function reChecked() {
  checkedFilterGlobal.forEach(function (item) {
    tempButton = document.getElementById(item);
    if (tempButton) {
      tempButton.click();
    }
  });
  tempSearchbar = document.getElementById("searchBar");
  tempSearchbar.value = searchVal;

  tempRadioBut = document.getElementById(radioVal);
  tempRadioBut.click();
}

function loadMorePapers() {
  const url = `../feed/generate_paper_feed.php?offset=${offset}&custom_query=${encodeURIComponent(
    searchQuery
  )}`;
  fetch(url)
    .then((response) => response.text())
    .then((html) => {
      const paperContainer = document.querySelector(".paper_top_paper");
      paperContainer.insertAdjacentHTML("beforeend", html);
      executeScriptsInElement(paperContainer);
      reChecked();
    })
    .catch((error) => {
      console.error("Error loading more papers:", error);
    });
}

function executeScriptsInElement(element) {
  const scripts = element.getElementsByTagName("script");
  for (let i = 0; i < scripts.length; i++) {
    const script = scripts[i];
    const scriptContent = script.textContent || script.innerText;
    const newScript = document.createElement("script");
    newScript.textContent = scriptContent;
    script.parentNode.replaceChild(newScript, script);
  }
  reChecked();
}

function hasScrolledPastPaper() {
  var paperDiv = document.getElementById("feedOverall");

  var paperRect = paperDiv.getBoundingClientRect();
  var bottomOfPaper = paperRect.bottom;

  return bottomOfPaper <= window.innerHeight;
}

let scrollTimeout;

function handleScroll() {
  if (scrollTimeout) {
    clearTimeout(scrollTimeout);
  }

  scrollTimeout = setTimeout(function () {
    if (hasScrolledPastPaper()) {
      if (isLogin || !initialFeed) {
        updateOffset();
        loadMorePapers();
        initialFeed = true;
      }
    }
  }, 200);
}

window.addEventListener("scroll", handleScroll);

function getCheckedCheckbox(filters) {
  const checkedFilters = [];

  filters.forEach((filter) => {
    const checkbox = document.querySelector(`input[name='${filter}']`);
    if (checkbox.checked) {
      checkedFilters.push(filter);
    }
  });

  return checkedFilters;
}

function getCheckedFilter() {
  var form = document.getElementById("sorting");
  var checkedFilter = form.querySelector('input[name="sortBy"]:checked');
  // console.log(checkedFilter.id);
  if (checkedFilter) {
    radioVal = checkedFilter.id;
    return checkedFilter.id;
  } else {
    return null;
  }
}

function searchFor() {
  const formData = new FormData();
  const inputSearchBar = document.getElementById("searchBar");
  formData.append("searchKey", inputSearchBar.value);
  searchVal = inputSearchBar.value;
  const filters = ["Title", "Author", "Tags"];

  const checkedFilters = getCheckedCheckbox(filters);
  checkedFilterGlobal = checkedFilters;
  checkedFilters.forEach((filter) => {
    formData.append(filter, true);
  });
  const checkedSorting = getCheckedFilter();
  formData.append("sort", checkedSorting);

  const endpoint = "search_logic.php";

  fetch(endpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((data) => {
      searchQuery = data;
      var papersContainer = document.getElementsByClassName("paper_for_each");

      var papersArray = Array.from(papersContainer);

      papersArray.forEach((paper) => {
        paper.remove();
      });
    })

    .catch((error) => {
      console.error("Error submitting data:", error);
    });
}

function goToEditPhp(postId) {
  const editEndpoint = `../postCreation/post_creation.php`;

  const newWindow = window.open(`${editEndpoint}?postId=${postId}`);

  if (
    !newWindow ||
    newWindow.closed ||
    typeof newWindow.closed === "undefined"
  ) {
    alert("Popup blocked. Please enable popups for this site.");
  }
}

function goToViewPhp(postId) {
  const viewEndpoint = `../view/view_account_file.php`;

  const newWindow = window.open(`${viewEndpoint}?PostId=${postId}`);

  if (
    !newWindow ||
    newWindow.closed ||
    typeof newWindow.closed === "undefined"
  ) {
    alert("Popup blocked. Please enable popups for this site.");
  }
}

function redirectToPage(index, UserId) {
  if (index == 0) {
    window.location.href = "../feed/home_page.php";
  } else if (index == 1) {
    window.location.href = `../Profile/account_view.php?UserId=${UserId}`;
  } else if (index == 2) {
    window.location.href = "../postCreation/post_creation.php";
  } else {
    var userConfirmed = confirm("Do you want to proceed with logging out?");
    if (userConfirmed) {
      window.location.href = "../logout.php";
    }
  }
}

function deleteThisPaper(postId) {
  var userConfirmation = confirm(
    "Are you sure you want to delete this post permanently?"
  );
  if (userConfirmation) {
    const formData = new FormData();
    formData.append("postId", postId);

    const endpoint = "deletePost.php";

    fetch(endpoint, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
      })
      .then((data) => {
        location.reload();
      })
      .catch((error) => {
        console.error("Error submitting data:", error);
      });
  }
}
function autoScroll() {
  const targetElement = document.getElementById("paper_top_paper");
  targetElement.scrollIntoView({
    behavior: "smooth",
    block: "end",
  });
}

function competitionLogic() {
  alert("Not yet implemented! Please comeback soon");
}
