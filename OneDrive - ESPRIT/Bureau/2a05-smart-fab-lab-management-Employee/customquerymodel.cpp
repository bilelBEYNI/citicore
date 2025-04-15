#include "customquerymodel.h"

customquerymodel::customquerymodel(QObject *parent)
    : QAbstractItemModel(parent)
{
}

QVariant customquerymodel::headerData(int section, Qt::Orientation orientation, int role) const
{
    // FIXME: Implement me!
}

QModelIndex customquerymodel::index(int row, int column, const QModelIndex &parent) const
{
    // FIXME: Implement me!
}

QModelIndex customquerymodel::parent(const QModelIndex &index) const
{
    // FIXME: Implement me!
}

int customquerymodel::rowCount(const QModelIndex &parent) const
{
    if (!parent.isValid())
        return 0;

    // FIXME: Implement me!
}

int customquerymodel::columnCount(const QModelIndex &parent) const
{
    if (!parent.isValid())
        return 0;

    // FIXME: Implement me!
}

QVariant customquerymodel::data(const QModelIndex &index, int role) const
{
    if (!index.isValid())
        return QVariant();

    // FIXME: Implement me!
    return QVariant();
}
